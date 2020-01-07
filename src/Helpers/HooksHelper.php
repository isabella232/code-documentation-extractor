<?php namespace YoastDocParser\Helpers;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use YoastDocParser\Visitors\HookVisitor;

/**
 * Class HooksHelper
 * @package YoastDocParser\Helpers
 */
class HooksHelper {
	/**
	 * @var array
	 */
	public static $hookFunctions = [
		'filters' => [
			'apply_filters',
			'apply_filters_ref_array',
			'apply_filters_deprecated',
		],
		'actions' => [
			'do_action',
			'do_action_ref_array',
			'do_action_deprecated',
		],
	];

	public static function getName( Node $node ) {
		if ( $node->name->getType() !== 'Name' ) {
			return '';
		}

		return $node->name->toString();
	}

	/**
	 * Determines whether or not the passed node is a filter.
	 *
	 * @param Node $node The node to check.
	 *
	 * @return bool Whether or not it's a filter.
	 */
	public static function isHook( Node $node ) {
		$name = self::getName( $node );

		if ( $name === '' ) {
			return false;
		}

		return in_array(
			$name,
			// Flattens the array
			array_merge(...array_values(self::$hookFunctions) ),
			true
		);
	}

	public static function isFilter( $node ) {
		return self::isHook( $node ) && in_array( self::getName( $node ), self::$hookFunctions['filters'], true );
	}

	public static function isAction( $node ) {
		return self::isHook( $node ) && in_array( self::getName( $node ), self::$hookFunctions['actions'], true );
	}

	public static function extractHooks( FunctionLike $object ) {
		// Possibly dealing with an abstract method.
		if ( $object->getStmts() === null ) {
			return [];
		}

		$traverser = new NodeTraverser();
		$traverser->addVisitor( new HookVisitor() );
		$nodes = $traverser->traverse( $object->getStmts() );

		$finder  = new NodeFinder();
		$filters = $finder->find( $nodes, function( Node $node ) {
			return $node instanceof Node\Expr\FuncCall && self::isHook( $node );
		} );

		return array_map( function( $filter ) {
			if ( $filter->hasAttribute( 'hook' ) ) {
				return $filter->getAttribute( 'hook' );
			}
		} , $filters );
	}
}
