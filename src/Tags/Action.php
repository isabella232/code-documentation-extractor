<?php namespace YoastDocParser\Tags;

use PhpParser\Comment\Doc;
use Webmozart\Assert\Assert;

/**
 * Class Action
 * @package YoastDocParser\Tags
 */
class Action extends BaseHookTag {

	/**
	 * @param string    $hookName
	 * @param mixed[][] $arguments
	 *
	 * @param Doc|null  $description
	 *
	 * @param bool $isDeprecated
	 *
	 * @psalm-param array<int, array<string, string|Type>|string> $arguments
	 */
	public function __construct(
		string $hookName,
		array $arguments = [],
		?Doc $description = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $hookName );

		parent::__construct( 'action', $hookName, $arguments, $description, $isDeprecated );
	}
}
