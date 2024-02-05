Hamlet Type 
===

![CI Status](https://github.com/hamlet-framework/type/workflows/CI/badge.svg?branch=master&event=push)
[![Packagist](https://img.shields.io/packagist/v/hamlet-framework/type.svg)](https://packagist.org/packages/hamlet-framework/type)
[![Packagist](https://img.shields.io/packagist/dt/hamlet-framework/type.svg)](https://packagist.org/packages/hamlet-framework/type)
[![Coverage Status](https://coveralls.io/repos/github/hamlet-framework/type/badge.svg?branch=master)](https://coveralls.io/github/hamlet-framework/type?branch=master)
![Psalm coverage](https://shepherd.dev/github/hamlet-framework/type/coverage.svg?)
[![Psalm level](https://shepherd.dev/github/hamlet-framework/type/level.svg?)](https://psalm.dev/)

## Motivation

The PHP type system is a complicated beast. PHP supports types in three ways: hinting, assertions, and casting.

To unwrap the complexity, it's easiest to start with the basic type `int`. The type hinting support is thorough; 
`int` can be used as a type hint for properties, arguments, return types, and constants. The assertion through type 
hints is both built-in during runtime and optional through static analyzers. The type assertion is done through 
the `is_int` function, which returns `true` if the value is an `int` and `false` otherwise. The type casting is done 
through the `(int)` cast, which returns an int if the value is an int and produces a warning otherwise. The casting is 
often implicit, see for example `5 + '6apples'` returning an `int(11)`. From that point of view, the support for `int` 
is complete.

The second, somewhat more complex type is a literal type, like `null` or `true` or `false`. Type hinting is supported. 
The assertion is done through the `===` operator. The conversion is missing but the semantics is pretty clear.

> It's a fine distinction here, but the equivalent of `if (is_int($x))` for integers is `if ($x === true)` for `true`, 
> and not `if ($x == true)` or `if ($x)`. The latter two expressions contain implicit casting, which changes the type 
> of the expression. There is no explicit casting to `null` or `true` or `false` as a library function. 
> The equivalent of `(null) $x` would be the following code:
> ```php
> function to_null($x)
> {
>     if ($x == null) {
>         return $x;
>     }
>     throw new Warning();
> }
> ```

Yet another case is again slightly more complex. Let's take a look at type `array-key`. It only exists as a type hint 
in Psalm and is not explicitly supported by PHP; the only type hinting possible is through PHPDoc. And yet, the type 
does "exist". PHP only allows integers and strings as array keys, and if there's a mismatch, tries to _convert_ the 
value. The assertion is straightforward: `is_int($key) || is_string($key)`. So, is `array-key` a union of `int` 
and `string`? Not quite. The implicit casting done by PHP is somewhat more complex and can be reverse-engineered:

```php
$a = [$yourKey => 1];
var_dump(array_key_first($a));
```

By running this expression, you'll see that there are conversions happening to `$yourKey` that are inconsistent with a 
cast "chain" to `int` and, if failed, to `string`:

```php
function to_array_key($x) {
    try {
        return (int) $x;
    } catch (Warning $e) {
        return (string) $x;
    }
}
```

So, while the assertion is simple, explicit, and obvious, the cast is not.

The next case is the `array` and variations thereof (`list`, `non-empty list`, `array<int>`, etc.). PHP's type hinting 
is limited to just `array`, so there's no support for non-emptiness, consecutive integer keys (list layout), key or 
element types. All additional variations should be specified through PHPDoc. The assertion by PHP is also limited to 
the very basic `is_array`. If you want to extend the assertion to check additional restrictions on the type, you'd need 
to write your own custom function like:

```php
function is_non_empty_list($value) {
    return is_array($value) && array_is_list($value) && count($value) > 0;
}
```

The casting support by PHP is also rather random. There's a wrapping of scalar values and unwrapping of objects, 
but there's no support for value casting like `(array<string>) [1, 2, 3]`.

The next case is classes. The type hinting is fully supported, and the assertion is done through `instanceof`. Casting 
is non-existent. Interestingly enough, the conversion from an associative array to an object of a specific class is one 
of the most common operations in PHP, and yet there's no built-in support for it. There are a few libraries that do 
that, including "json-mapping", but the semantics of such a cast are bespoke.

The next case is union types. There's hinting support by PHP for the union itself. The completeness of the hinting 
support for union types crucially depends on the support for the types that are being unioned. For example, `int|null` 
is as complete as it gets, and `array<int>|array<string>` is only supported through PHPDoc. The same applies to 
assertions. The `is_int($a) || is_null($a)` is good enough in the first case, but there's no built-in equivalent in PHP 
to assert the type `array<int>|array<string>`. There's also no casting.

The lack of casting support for union types is not surprising. If we treat `int|string` as the same type as 
`string|int`, and we should, then there's no simple way to reconcile `(int|string) $a === (string|int) $a` for all `$a`.
The proper way of handling casting to union types should likely be:

```php
if (!$this->match($value)) {
    throw new CastException();
}
return $value;
```

For the practical reasons these last condition can be relaxed around `null`. The nullable types are a special case of a 
union type `T|null`, and can be unambiguously resolved in two steps: check if the value is same as `null`, 
if not try to cast it to `T`.

The last case is generics and callables. There's no support for generics in PHP as of now, and yet they are implicitly 
used by a lot of tools these days. The hinting support may come one day. The assertion support is doubtful beyond 
some special cases like `array`. For example, it's not possible to assert beforehand that an endless generator is of 
type `iterable<int>`.

```php
function ints(): iterable {
    $i = 0;
    while (true) {
        yield $i++;
        if (cosmic_ray()) {
            yield "haha";
        }
    }
}
```

The same applies to callables. The hinting support is there, but assertions and casting are likely not possible.

Numeric string is a string which is numeric. The type hints are supported by Psalm, the assertion is as simple as 
`is_string($a) && is_numeric($a)`. But as for the casting, there's no built-in support for it. The semantics of it is 
casting of the value to a string and checking that it's numeric. Other interpretations are possible, so this is a bit 
messy.

## Library support for hinting, assertions and casting

The library provides a consistent support for hinting, assertions and casting as much as possible. 

For type hints there are two options. 

Firstly, you can construct types by using type constructors:

```php
$type = _map(_int(), _union(_class(DateTime::class), _null()));
```

Secondly, for libraries and metaprogramming you can parse PHPDoc or type expression by calling `type_of`.

```php
$type = type_of('array<int,DateTime|null>');
```

The support for assertions is provided by the `match` and `assert` methods. The first method returns `true` if the value
confirms to type specification (as much as it's possible to infer at run time). The second method (a convenience one) works as a pass-through
that throws an assertion exception if there's no match.

```php
$a = _non_empty_list(_int())->assert($properties['a']);
```

This will add assertion on the type of `$a` being `non-empty-list<int>`, and tell Psalm that it's the type of `$a` now. 

The support for casts is ... @todo add resolver explanation.

## Invariant

The motivation of this library to provide a consistent and complete support for PHP types.  Given a type hint, for 
example `array<int,string>`:

- The `match` method should return `true` only for an `array` where every key `is_int` and every value `is_string`. 
  The semantics of `match` is as consistent with PHP and Psalm documentation as possible. No casting is allowed.
- The `assert` method should throw an assertion exception if the type does not `match` the type hint.
- The `cast` method consistently extends the semantics of `(int)`, `(string)` and other PHP built in casts for the rest
  of the types. The `cast` method should throw a cast exception if the type cannot be cast to the target type.

The invariants of the type algebra should be:

    $type->match($type->cast($value)) === true 
    // or an CastException is thrown

    if ($type->match($value)) {
        $value == $type->cast($value); (Note that for most types, it's `===`, except array-key)
    }
    // and no CastException can be thrown

## Type Overview

Types supported:

    | Type                   | Function                   |
    +------------------------+----------------------------+
    | mixed                  | _mixed()                   |
    | int                    | _int()                     |
    | float                  | _float()                   |
    | string                 | _string()                  |
    | non-empty-string       | _non_empty_string()        |
    | numeric-string         | _numeric_string()          |
    | bool                   | _bool()                    |
    | numeric                | _numeric()                 |
    | array_key              | _array_key()               |
    | null                   | _null()                    |
    | 'a'|1|false            | _literal('a', 1, false)    |
    | object                 | _object()                  |
    | resource               | _resource()                | 
    | UserType               | _class(UserType::class)    |
    | int|float              | _union(_int(), _float())   |
    | list{int,string}       | _tuple(_int(), _string())  |
    | array<string>          | _array(_string())          |
    | list<id>               | _list(_id())               | 
    | non-empty-array<int>   | _non_empty_array(_int())   |
    | non-empty-list<string> | _non_empty_list(_string()) |
    | array<int,string>      | _map(_int(), _string())    |

## Background

- Move completely to PHPStan parser, including docblock
- Add intersection types
- Support for enums
- Support for the rest of scalar types: https://psalm.dev/docs/annotating_code/type_syntax/scalar_types/
- Support for iterable|self|static|class-string|interface-string
- Maybe, support for callables and object-like-arrays, in terms of parsing, but not algebra
