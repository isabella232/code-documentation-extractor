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
abstract class BaseHookTag extends BaseTag implements StaticMethod {

	/**
	 * @var string
	 */
	protected $name = '';

	/** @var string[][] */
	private $arguments = [];

	/** @var Type */
	private $returnType;

	/**
	 * @var string
	 */
	private $tagName;

	/**
	 * @var bool
	 */
	private $isDeprecated;

	/**
	 * Constructs the BaseHookTag.
	 *
	 * @param string    $name         The name of the tag.
	 * @param string    $tagName      The tag's name.
	 * @param mixed[][] $arguments    The tag's arguments.
	 * @param Doc|null  $description  The tag's description.
	 * @param bool      $isDeprecated Whether or not the tag is deprecated.
	 */
	public function __construct(
		string $name,
		string $tagName,
		array $arguments = [],
		?Doc $description = null,
		bool $isDeprecated = false
	) {
		Assert::stringNotEmpty( $name );
		Assert::stringNotEmpty( $tagName );

		$returnType = null; // TODO parse this via regex?

		if ($returnType === null) {
			$returnType = new Void_();
		}

		$this->name         = $name;
		$this->tagName      = $tagName;
		$this->arguments    = $arguments;
		$this->returnType   = $returnType;
		$this->description  = $description;
		$this->isDeprecated = $isDeprecated;
	}

	/**
	 * @inheritDoc
	 */
	public static function create( string $body ) {
		// TODO: Implement create() method.
	}

	/**
	 * Gets the tag's name.
	 *
	 * @return string The tag's name.
	 */
	public function __toString(): string {
		return $this->tagName;
	}

	/**
	 * Creates a new instance of the tag.
	 *
	 * @param Node     $node         The node to base the new tag on.
	 * @param Doc|null $description  The documentation associated with the node.
	 * @param bool     $isDeprecated Whether or not the node is deprecated.
	 *
	 * @return static The new instance.
	 */
	public static function fromNode( Node $node, Doc $description = null, bool $isDeprecated = false ) {
		$printer = new PrettyPrinter();

		$args = array_map( function( $arg ) { return $arg; }, $node->args );

		array_shift( $args );

		return new static(
			$printer->prettyPrintExpr( $node->args[0]->value ),
			$args,
			$description,
			$isDeprecated
		);
	}

	protected function detectReturnType( $description ) {

	}
}
