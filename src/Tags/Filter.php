<?php namespace YoastDocParser\Tags;

use PhpParser\Comment\Doc;
use Webmozart\Assert\Assert;

/**
 * Class Filter
 * @package YoastDocParser\Tags
 */
class Filter extends BaseHookTag {

	/**
	 * @param string    $filterName
	 * @param mixed[][] $arguments
	 *
	 * @param Doc|null  $description
	 *
	 * @param bool $isDeprecated
	 *
	 * @psalm-param array<int, array<string, string|Type>|string> $arguments
	 */
	public function __construct(
		string $filterName,
		array $arguments = [],
		?Doc $description = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty($filterName);

		parent::__construct( 'filter', $filterName, $arguments, $description, $isDeprecated );
	}


//	public static function prepareName( Node $node ) {
//		$printer = new PrettyPrinter\Standard();
//
//		return $this->cleanupName(
//			$printer->prettyPrintExpr( $node->args[0]->value )
//		);
//	}
//
//	public function getShortName() {
//		return $this->name;
//	}
//
//	public function getType() {
//		$type = 'filter';
//		switch ( $this->name ) {
//			case 'do_action':
//				$type = 'action';
//				break;
//			case 'do_action_ref_array':
//				$type = 'action_reference';
//				break;
//			case 'do_action_deprecated':
//				$type = 'action_deprecated';
//				break;
//			case 'apply_filters_ref_array':
//				$type = 'filter_reference';
//				break;
//			case 'apply_filters_deprecated';
//				$type = 'filter_deprecated';
//				break;
//		}
//
//		return $type;
//	}
//
//	public function getArgs( Node $node ) {
//		$printer = new PrettyPrinter\Standard();
//		$args    = [];
//		foreach ( $node->args as $arg ) {
//			$args[] = $arg;
//		}
//
//		// Skip the filter name
//		array_shift( $args );
//
//		return $args;
//	}
//
//	public function getDocComment() {
//		return $this->docblock;
//	}
//
//	private function cleanupName( string $name ) {
//		$matches = [];
//
//		// quotes on both ends of a string
//		if ( preg_match( '/^[\'"]([^\'"]*)[\'"]$/', $name, $matches ) ) {
//			return $matches[1];
//		}
//
//		// two concatenated things, last one of them a variable
//		if ( preg_match(
//			'/(?:[\'"]([^\'"]*)[\'"]\s*\.\s*)?' . // First filter name string (optional)
//			'(\$[^\s]*)' .                        // Dynamic variable
//			'(?:\s*\.\s*[\'"]([^\'"]*)[\'"])?/',  // Second filter name string (optional)
//			$name, $matches ) ) {
//
//			if ( isset( $matches[3] ) ) {
//				return $matches[1] . '{' . $matches[2] . '}' . $matches[3];
//			}
//
//			return $matches[1] . '{' . $matches[2] . '}';
//		}
//
//		return $name;
//	}
}
