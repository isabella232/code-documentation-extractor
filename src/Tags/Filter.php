<?php namespace YoastDocParser\Tags;

use PhpParser\Comment\Doc;
use Webmozart\Assert\Assert;

/**
 * Class Filter
 * @package YoastDocParser\Tags
 */
class Filter extends BaseHookTag {

	/**
	 * Filter constructor.
	 *
	 * @param string    $filterName The filter's name.
	 * @param mixed[][] $arguments The filter's arguments.
	 *
	 * @param Doc|null  $description The filter's description.
	 *
	 * @param bool $isDeprecated Whether or not the filter is flagged as deprecated.
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
