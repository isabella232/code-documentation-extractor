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
}
