<?php namespace YoastDocParser\WordPress;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PluginFinder
 * @package YoastDocParser\WordPress
 */
class PluginFinder {

	/**
	 * @var array
	 */
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

	/**
	 * @var array|IteratorAggregate
	 */
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
			$pluginData = $this->collectPluginData( $file );

			if ( ! empty( $pluginData['Name'] ) ) {
				$plugin = new Plugin(
					$pluginData['Name'],
					$pluginData['PluginURI'],
					$pluginData['Version'],
					$pluginData['Description'],
					new Author( $pluginData['Author'], $pluginData['AuthorURI'] ),
					new Textdomain( $pluginData['TextDomain'], $pluginData['DomainPath'] ),
					$pluginData['Network']
				);
				break;
			}
		}

		return $plugin;
	}

	/**
	 * Collects the plugin data from the passed file.
	 *
	 * @param SplFileInfo $file The file to collect the data fromm
	 *
	 * @return array The plugin data. Can be an empty array if the file isn't considered a valid plugin file.
	 */
	protected function collectPluginData( SplFileInfo $file ) {
		$pluginData = [];
		$fileContents = $file->getContents();

		foreach ( $this->headers as $field => $header ) {
			$pluginData[ $field ] = $this->extractHeaderData( $fileContents, $header );
		}

		return $pluginData;
	}

	/**
	 * Extracts the data from the passed file contents based on the passed header.
	 *
	 * @param string $fileContents 	The content to extract the plugin data from.
	 * @param string $header 		The header to match against.
	 *
	 * @return string The extracted data.
	 */
	protected function extractHeaderData( $fileContents, $header ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $header, '/' ) . ':(.*)$/mi', $fileContents, $match ) && $match[1] ) {
			return $this->cleanup( $match[1] );
		}

		return '';
	}

	/**
	 * Cleans up any stray spaces and other characters from the passed string.
	 *
	 * @param string $string The string to clean up.
	 *
	 * @return string The cleaned string.
	 */
	protected function cleanup( string $string ) {
		return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $string ) );
	}

}
