<?php namespace YoastDocParser\Tags;

use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Void_;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use Webmozart\Assert\Assert;

/**
 * Class BaseHookTag
 * @package YoastDocParser\Tags
 */
class BaseHookTag extends BaseTag implements StaticMethod {

	protected $name = '';

	/** @var string[][] */
	private $arguments = [];

	/** @var Type */
	private $returnType;

	/**
	 * @var string
	 */
	private $itemName;

	/**
	 * @var bool
	 */
	private $isDeprecated;

	/**
	 * @param string    $name The name of the tag.
	 * @param string    $itemName
	 * @param mixed[][] $arguments
	 *
	 * @param Doc|null  $description
	 *
	 * @param bool      $isDeprecated
	 *
	 * @psalm-param array<int, array<string, string|Type>|string> $arguments
	 */
	public function __construct(
		string $name,
		string $itemName,
		array $arguments = [],
		?Doc $description = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $name );
		Assert::stringNotEmpty( $itemName );

		$returnType = null; // TODO parse this via regex?

		if ($returnType === null) {
			$returnType = new Void_();
		}

		$this->name = $name;
		$this->itemName = $itemName;
		$this->arguments   = $arguments;
		$this->returnType  = $returnType;
		$this->description = $description;
		$this->isDeprecated = $isDeprecated;
	}

	/**
	 * @inheritDoc
	 */
	public static function create( string $body ) {
		// TODO: Implement create() method.
	}

	public function __toString(): string {
		return $this->itemName;
	}

	public static function fromNode( Node $node, Doc $lastDoc = null, bool $isDeprecated = false ) {
		$printer = new PrettyPrinter();

		$args = array_map( function( $arg ) { return $arg; }, $node->args );

		array_shift( $args );

		return new static(
			$printer->prettyPrintExpr( $node->args[0]->value ),
			$args,
			$lastDoc,
			$isDeprecated
		);
	}

	protected function detectReturnType( $description ) {

	}
}
