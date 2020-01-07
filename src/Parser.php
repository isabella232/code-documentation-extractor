<?php namespace YoastDocParser;

use Error;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\Argument;
use phpDocumentor\Reflection\Php\Factory\Class_;
use phpDocumentor\Reflection\Php\Factory\Constant;
use phpDocumentor\Reflection\Php\Factory\DocBlock;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\Interface_;
use phpDocumentor\Reflection\Php\Factory\Property;
use phpDocumentor\Reflection\Php\Factory\Trait_;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use YoastDocParser\Factories\Function_;
use YoastDocParser\Factories\Method;
use YoastDocParser\Helpers\FileHelper;
use YoastDocParser\WordPress\NullPlugin;
use YoastDocParser\WordPress\PluginFinder;

/**
 * Class Parser
 * @package YoastDocParser
 */
class Parser {
	/**
	 * @var array
	 */
	private $ignore = [
		'vendor',
		'vendor_prefixed',
		'node_modules',
		'integration-tests',
		'tests',
		'build',
		'config',
		'grunt',
		'deploy_keys',
		'js',
		'languages',
		'webpack',
		'images',
		'css',
	];

	/**
	 * @var Finder|Error
	 */
	private $files;

	/**
	 * @var WordPress\NullPlugin|WordPress\Plugin
	 */
	private $pluginData;

	/**
	 * @var \PhpParser\Parser
	 */
	private $parser;

	/**
	 * @var string
	 */
	private $directory;
	/**
	 * @var OutputInterface
	 */
	private $outputter;

	/**
	 * Parser constructor.
	 *
	 * @param string               $directory The directory to parse.
	 *
	 * @param OutputInterface $outputter
	 */
	public function __construct( string $directory, OutputInterface $outputter ) {
		if ( ! is_dir( $directory ) ) {
			throw new Error( 'The passed directory is invalid.' );
		}

		$this->directory = $directory;
		$files = FileHelper::get_files( $directory, $this->ignore );

		$this->files = $files;

		$this->parser = ( new ParserFactory() )->create( ParserFactory::PREFER_PHP5 );

		$this->outputter = $outputter;
	}

	/**
	 * @throws \phpDocumentor\Reflection\Exception
	 */
	public function parse() {
		// First attempt to collect plugin data.
		$this->pluginData = $this->findPluginData();

		$this->outputter->writeln( 'Successfully found plugin data' );

		$factory = new ProjectFactory( [
			new Argument(new PrettyPrinter()),
			new Class_(),
			new Constant(new PrettyPrinter()),
			new DocBlock( DocBlockFactory::createInstance() ),
			new File( NodesFactory::createInstance() ),
			new Function_( new \phpDocumentor\Reflection\Php\Factory\Function_() ),
			new Interface_(),
			new Method( new \phpDocumentor\Reflection\Php\Factory\Method() ),
			new Property(new PrettyPrinter()),
			new Trait_(),
		] );

		$files = array_map( function( $file ) {
			return new LocalFile( $file );
		}, array_keys( iterator_to_array( $this->files ) ) );

		$start   = microtime( true );
		$project = $factory->create( 'YoastSEO', $files );
		$end     = microtime( true );

		$this->outputter->writeln( sprintf( 'Parsed %d files in %s seconds',
			count( $files ),
			( $end - $start )
		) );
	}

	protected function findPluginData() {
		if ( ! empty( $this->pluginData ) ) {
			throw new RuntimeException( 'Plugin data already set. You cannot set this data twice.' );
		}

		$pluginData = ( new PluginFinder( $this->files ) )->find();

		if ( $pluginData instanceof NullPlugin ) {
			throw new RuntimeException( 'No plugin data could be found. Are you parsing the correct directory?' );
		}

		return $pluginData;
	}
}
