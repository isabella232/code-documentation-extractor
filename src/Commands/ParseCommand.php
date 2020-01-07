<?php namespace YoastDocParser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use YoastDocParser\Parser;

/**
 * Class ParseCommand
 * @package YoastDocParser
 */
class ParseCommand extends Command {
	/**
	 * @var string
	 */
	protected static $defaultName = 'parse';

	/**
	 * @var string
	 */
	private $directory;

	protected function configure() {
		$this->setDescription( 'Runs the parser.' )
			 ->setHelp( 'This command runs the parser.' );

		// Arguments
		$this->addOption( 'directory', '-d', InputOption::VALUE_OPTIONAL, 'The plugin directory', '' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$directory = $input->getOption( 'directory' );
		$helper = $this->getHelper( 'question' );

		// Check for empty directory.
		if ( empty( $directory ) ) {
			$question = new Question( 'Please enter the directory of the plugin you want to parse: ', '' );
			$question->setValidator( function( $answer ) {
				if ( ! is_string( $answer ) || empty( $answer ) ) {
					throw new \RuntimeException( 'The directory cannot be empty' );
				}

				return $answer;
			} );

			$directory = $helper->ask( $input, $output, $question );
		}

		$output->writeln( 'Parsing plugin in directory: ' . $directory );
		$parser = new Parser( $directory, $output );
		$parser->parse();

		return 0;
	}
}
