<?php namespace YoastDocParser\Visitors;

use phpDocumentor\Reflection\DocBlock\Tag;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use YoastDocParser\Helpers\HooksHelper;
use YoastDocParser\Tags\Action;
use YoastDocParser\Tags\Filter;

/**
 * Class HookVisitor
 * @package YoastDocParser\Visitors
 */
class HookVisitor extends NodeVisitorAbstract {
	/**
	 * @var Tag[] $tags
	 */
	private $hooks = [];

	/**
	 * @var Node|null
	 */
	private $lastVisited = null;

	/**
	 * Visits the passed node in search of hook information.
	 *
	 * @param Node $node	The node to visit.
	 *
	 * @return Node|void|null The resulting node or null if nothing was changed.
	 */
	public function enterNode( Node $node ) {
		if ( ! $this->isHookNode( $node ) && $this->hasDocumentation( $node ) ) {
			$this->registerLastVisited( $node );

			return;
		}

		if ( $this->isHookNode( $node ) ) {
			if ( HooksHelper::isFilter( $node ) ) {
				$this->hooks = Filter::fromNode(
					$node,
					$this->getLastDocComment(),
					$this->isDeprecated( $node )
				);
			}

			if ( HooksHelper::isAction( $node ) ) {
				$this->hooks = Action::fromNode(
					$node,
					$this->getLastDocComment(),
					$this->isDeprecated( $node )
				);
			}

			$this->registerLastVisited( $node );
		}
	}

	/**
	 * Leaves the node.
	 *
	 * @param Node $node The node to leave.
	 *
	 * @return Node|null The node.
	 */
	public function leaveNode( Node $node ) {
		if ( ! $this->isHookNode( $node ) ) {
			return $node;
		}

		$node->setAttribute( 'hook', $this->hooks );

		return $node;
	}

	/**
	 * Gets the last known Doc object.
	 *
	 * @return \PhpParser\Comment\Doc|null The last known Doc or null if none was set.
	 */
	protected function getLastDocComment() {
		if ( $this->lastVisited === null ) {
			return null;
		}

		return $this->lastVisited->getDocComment();
	}

	/**
	 * Checks whether the passed node has documentation.
	 *
	 * @param Node $node The node to check.
	 *
	 * @return bool Whether or not the node has documentation.
	 */
	protected function hasDocumentation( Node $node ) {
		return $node->getType() !== 'Name' && $node->getDocComment();
	}

	/**
	 * Registers the last visited node.
	 *
	 * @param Node $node The node that was visited last.
	 */
	protected function registerLastVisited( Node $node ) {
		$this->lastVisited = $node;
	}

	/**
	 * Determines whether the node is documentable.
	 *
	 * @param Node $node The node to check.
	 *
	 * @return bool Whether or not the node is documentable.
	 */
	protected function isHookNode( Node $node ) {
		return $node instanceof Node\Expr\FuncCall && HooksHelper::isHook( $node );
	}

	/**
	 * Determines whether the node is deprecated.
	 *
	 * @param Node $node The node to check.
	 *
	 * @return bool Whether or not the node is considered deprecated.
	 */
	protected function isDeprecated( Node $node ) {
		return strpos( HooksHelper::getName( $node ), '_deprecated' ) !== false;
	}
}
