# Vicimus Standard #

This is a phpcs implementation of the Vicimus Coding Standard. Combined with
[the PSR2 coding standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), the
Vicimus Coding Standard fills in some holes that PSR2 does not cover (mainly PHP 7 related things, but a few others).

*It's worth noting that many of these rules/sniffs are from existing phpcs
code standard sniffs, and thus many of the rule descriptions are directly
taken from their documentation.*

# Rules/Sniffs #

The following rules expand upon PSR2s coding standards.





## Arrays #

### Disallow Long Array Syntax #

Ensures that all array definitions use the short syntax `[]` instead of long `array()`.

### Trailing Array Comma #

Commas after last element in an array make adding a new element easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line arrays and requires short array syntax `[]`.









## Classes #

### Class Constant Visibility #

In PHP 7.1 it's possible to declare visibility of class constants. In a
similar vein to optional declaration of visibility for properties and methods
which is actually required in sane coding standards, this sniff also requires
to declare visibility for all class constants.

```
const FOO = 1; // visibility missing!
public const BAR = 2; // correct
```

### Doc Comment #

Ensures doc blocks follow basic formatting. This checks to make sure there
is proper spacing, line breaks, etc. on all docblock content.

### Function Call Argument Spacing #

Ensures proper spacing between function arugments.

### Lowercase Constants #

Kind of redunant but included, ensures constants are not lowercase.

### Unused Private Elements #

Checks for unused methods, unused or write-only properties in a class and
unused private constants. Reported unused elements are safe to remove.

This is very useful during refactoring to clean up dead code and injected dependencies.

### Uppercase Constants #

Ensures constants are declared in uppercase.








## Formatting #

### Disallow Tab Indents #

Ensures all indenting is done with spaces not tabs

### Doc Comment Alignment #

Ensures all doc comments use proper alignment and spacing between params and
return values, with type, name and descriptions.

```
// Good --
/**
 * A method!
 *
 * @param string $value  This is the value to pass
 * @param int    $number Another parameter here
 *
 * @return Response
 */

 // Bad --
 /**
  * A method!
  *
  * @param  string $value
  * @param int $number Another parameter here
  *
  * @return Response
  */
```

### Function Comments #

Ensures all methods are commented with accurate doc blocks.

### Inline Doc Comment Declaration #

Reports invalid format of inline phpDocs with @var.

### Line Length Limits #

Enforces a soft character limit of 80 characters, and will throw an error if
it exceeds 120.







## Type Hints #

### Declare Strict Types #

Enforces having declare(strict_types = 1) at the top of each PHP file. Should
follow this example:

```
<?php declare(strict_types = 1);

namespace ...
```

For more information on this declaration,
[read the section in the manual](http://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.strict).


### Long Type Hints #

Enforces using shorthand scalar typehint variants in phpDocs: int instead
of integer and bool instead of boolean. This is for consistency with native
scalar typehints which also allow shorthand variants only.

### Nullable Type For Null Default Value #

Checks whether the nullablity ? symbol is present before each nullable and
optional parameter (which are marked as = null):

```
function foo(
    int $foo = null, // ? missing
    ?int $bar = null // correct
) {

}
```

### Parameter Type Hint Spacing #

- Checks that there's a single space between a typehint and a parameter name: Foo $foo
- Checks that there's no whitespace between a nullability symbol and a typehint: ?Foo

### Return Type Hint Spacing #

Enforces consistent formatting of return typehints, like this:

```
    function foo(): ?int
```

### Scalar Type Hint Missing #

Ensures that all method parameters that are scalar values (string, bool, int) are
using the scalar type hints when possible.

### Type Hint Declaration #

- Checks for missing property types in phpDoc @var.

- Checks for missing typehints in case they can be declared natively. If the
  phpDoc contains something that can be written as a native PHP 7.0 or 7.1 typehint,
  this sniff reports that.

- Checks for missing @return and/or native return typehint in case the method
  body contains return with a value.

- Checks for useless doc comments. If the native method declaration contains
  everything and the phpDoc does not add anything useful, it's reported as
  useless and can optionally be automatically removed with phpcbf.

- Some phpDocs might still be useful even if they do not add any typehint
  information. They can contain textual descriptions of code elements and
  also some meaningful annotations like @expectException or @dataProvider.

- Forces to specify what's in traversable types like `array`, `iterable` and
  `\Traversable`.

### Type Hint Missing #

Ensures that all method parameters are using type hints when possible.









## Exceptions #

### Dead Catch #

This sniffs finds unreachable catch blocks:

```
try {
    doStuff();
} catch (\Throwable $e) {
    log($e);
} catch (\InvalidArgumentException $e) {
    // unreachable!
}
```

### Reference Throwable Only #

In PHP 7.0, a `Throwable` interface was added that allows catching and
handling errors in more cases than `Exception` previously allowed. So if the
catch statement contained `Exception` on PHP 5.x, it means it should probably
be rewritten to reference `Throwable` on PHP 7.x.








## Control Structures #

### Assignment In Condition #

Disallows assignments in if, elseif and do-while loop conditions:

```
if ($file = findFile($path)) {

}
```

Assignment in while loop condition is specifically allowed because it's commonly used.

### Disallow Equal Operators #

Disallows using loose == and != comparison operators. Use === and !== instead,
they are much more secure and predictable.








## Namespaces #

### AlphabeticallySortedUses #

Checks whether uses at the top of a file are alphabetically sorted. Follows
natural sorting and takes edge cases with special symbols into consideration.
The following code snippet is an example of correctly sorted uses:

```
use LogableTrait;
use LogAware;
use LogFactory;
use LoggerInterface;
use LogLevel;
use LogStandard;
```

### Disallow Group Use #

Group use declarations are ugly, make diffs ugly and this sniffs prohibits them.
An example of what not to do can be found in [the official RFC](https://wiki.php.net/rfc/group_use_declarations).

### Multiple Uses Per Line #

Prohibits multiple uses separated by commas:

```
use Foo, Bar;
```

### Reference Used Names Only #

Enforces to use all referenced names. What this means is, instead of using
fully qualified class names, you must `use` them at the top of the documentat
**always**, and then refer to them by the short names.

### Unused Uses #

Looks for unused imports from other namespaces.

### Use Does Not Start With Backslash #

Disallows leading backslash in use statement:

```
use \Foo\Bar;
```

### Use From Same Namespace #

Prohibits uses from the same namespace:

```
namespace Foo;

use Foo\Bar;
```

Because you are already in the `Foo` namespace, the use statement is
redundant and adds to visual debt.
