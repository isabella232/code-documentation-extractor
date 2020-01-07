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

	public function leaveNode( Node $node ) {
		if ( ! $this->isHookNode( $node ) ) {
			return $node;
		}

		$node->setAttribute( 'hook', $this->hooks );

		return $node;
	}

	protected function getLastDocComment() {
		if ( $this->lastVisited === null ) {
			return null;
		}

		return $this->lastVisited->getDocComment();
	}

	protected function hasDocumentation( Node $node ) {
		return $node->getType() !== 'Name' && $node->getDocComment();
	}

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

	protected function isDeprecated( Node $node ) {
		return strpos( HooksHelper::getName( $node ), '_deprecated' ) !== false;
	}
}
