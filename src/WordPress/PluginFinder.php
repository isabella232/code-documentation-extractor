<?php namespace YoastDocParser\WordPress;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PluginFinder
 * @package YoastDocParser\WordPress
 */
class PluginFinder {

	private $headers = [
		'Name'        => 'Plugin Name',
		'PluginURI'   => 'Plugin URI',
		'Version'     => 'Version',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'TextDomain'  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
		'Network'     => 'Network',
		'RequiresWP'  => 'Requires at least',
		'RequiresPHP' => 'Requires PHP',
	];

	private $files;

	/**
	 * PluginFinder constructor.
	 *
	 * @param array|IteratorAggregate $files The files to search through.
	 */
	public function __construct( $files ) {
		$this->files = $files;
	}

	/**
	 * Finds the plugin data and returns a Plugin object, if data is found.
	 **
	 * @return NullPlugin|Plugin The plugin data.
	 */
	public function find() {
		$plugin = new NullPlugin();

		foreach ( $this->files as $file ) {
			$plugin_data = $this->collectPluginData( $file );

			if ( ! empty( $plugin_data['Name'] ) ) {
				$plugin = new Plugin(
					$plugin_data['Name'],
					$plugin_data['PluginURI'],
					$plugin_data['Version'],
					$plugin_data['Description'],
					new Author( $plugin_data['Author'], $plugin_data['AuthorURI'] ),
					new Textdomain( $plugin_data['TextDomain'], $plugin_data['DomainPath'] ),
					$plugin_data['Network']
				);
				break;
			}
		}

		return $plugin;
	}

	protected function collectPluginData( SplFileInfo $file ) {
		$pluginData = [];

		foreach ( $this->headers as $field => $regex ) {
			$pluginData[ $field ] = $this->getPluginData( $file->getContents(), $regex );
		}

		return $pluginData;
	}

	protected function getPluginData( $file_data, $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
			return $this->cleanup( $match[1] );
		}

		return '';
	}

	protected function cleanup( string $string ) {
		return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $string ) );
	}

}
