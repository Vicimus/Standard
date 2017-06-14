# Vicimus Standard #

This is a phpcs implementation of the Vicimus Coding Standard. Combined with
[the PSR2 coding standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), the
Vicimus Coding Standard fills in some holes that PSR2 does not cover (mainly PHP 7 related things, but a few others).

## Rules/Sniffs #

The following rules expand upon PSR2s coding standards.

### Doc Comment #

**Ensures doc blocks follow basic formatting**. This checks to make sure there
is proper spacing, line breaks, etc. on all docblock content.

### Type Hint Missing #

Ensures that all method parameters are using type hints when possible.

### Scalar Type Hint Missing #

Ensures that all method parameters that are scalar values (string, bool, int) are
using the scalar type hints when possible.

### Disallow Long Array Syntax #

Ensures that all array definitions use the short syntax `[]` instead of long `array()`.

### Function Call Argument Spacing #

Ensures proper spacing between function arugments.

### Uppercase Constants #

Ensures constants are declared in uppercase.

### Lowercase Constants #

Kind of redunant but included, ensures constants are not lowercase.

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

### Line Length Limits #

Enforces a soft character limit of 80 characters, and will throw an error if
it exceeds 120.

### TypeHintDeclaration #

### ReferenceThrowableOnly #

### DeclareStrictTypes #

### AssignmentInCondition #

### DisallowEqualOperators #

### UnusedPrivateElements #

### UnusedUses #

### UseFromSameNamespace #

### DeadCatch #

### TrailingArrayComma #

### AlphabeticallySortedUses #

### LongTypeHints #

### ClassConstantVisibility #

### ReturnTypeHintSpacing #

### NullableTypeForNullDefaultValue #

### ParameterTypeHintSpacing #

### DisallowGroupUse #

### MultipleUsesPerLine #

### ReferenceUsedNamesOnly #

### UseDoesNotStartWithBackslash #

### InlineDocCommentDeclaration #
