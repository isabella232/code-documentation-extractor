<?php namespace YoastDocParser\Tags;

use PhpParser\Comment\Doc;
use Webmozart\Assert\Assert;

/**
 * Class Action
 * @package YoastDocParser\Tags
 */
class Action extends BaseHookTag {

	/**
	 * Action constructor.
	 *
	 * @param string    $actionName   The action name.
	 * @param mixed[][] $arguments    The action's arguments.
	 *
	 * @param Doc|null  $description  The action's description.
	 *
	 * @param bool      $isDeprecated Whether or not the action is flagged as deprecated.
	 */
	public function __construct(
		string $actionName,
		array $arguments = [],
		?Doc $description = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $actionName );

		parent::__construct( 'action', $actionName, $arguments, $description, $isDeprecated );
	}
}
