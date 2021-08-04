Hamlet Cast 
===

[![Build Status](https://travis-ci.org/hamlet-framework/type.svg)](https://travis-ci.org/hamlet-framework/type)

There are few aspects of specifying type of expression in PHP:

1. The most exact specification of the type (we assume it's in psalm syntax): `array<int,DateTime|null>`
2. The sequence of run time assertions: `assert($records instanceof array<int,DateTime|null>)`
3. The type hint for static analysers (which is _psalm_ at the moment): `(array<int,DateTime|null>) $records`
4. The ability to derive types without string manipulation: `array<int,DateTime|null> || null`
5. The ability to cast when it's safe, i.e. falsey should cast to false, etc.

This library provides the basic building blocks for type specifications. For example, the following expression:

```php
$type = _map(
  _int(), 
  _union(
    _class(DateTime::class), 
    _null()
  )
)
```

creates an object of type `Type<array<int,DateTime|null>>`.

Asserting at run time, that the type of `$records` is `array<int,DateTime|null>>`:
```php
$type->assert($records);
```

Cast `$records` to `array<int,DateTime|null>>` and throw an exception when `$records` cannot be cast to `array<int,DateTime|null>>`:
```php
return $type->cast($records);
```

Combine type with other types, for example, making it nullable `array<int,DateTime|null>>|null`:
```php
_union($type, _null())
```

## Object like arrays

Object like arrays require more leg work. For example the type `array{id:int,name:string,valid?:bool}` 
corresponds to this construct:

```php
/** @var Type<array{id:int,name:string,valid?:bool}> */
$type = _object_like([
    'id'     => _int(),
    'name'   => _string(),
    'valid?' => _bool()
]);
``` 

Quite a mouthful. Also, note the required `@var` as psalm currently have no support for dependent types. 

If you want to assert types matching your PHPDoc you can use the type parser (WiP):

```php
/** @var Type<array{id:int}> */
$type = Type::of('array{id:int}');

assert($type->matches($record));
```

## Background

PHP Type "system" is a soup. There are multiple ingredients that cause its unforgettable taste:

- Different versions of PHP
- Super globals
- Patchy support for types in PHP
- Default casts
- Largely untyped core PHP library with baroque API surface
- Outdated PHPDoc specification
- Uncoordinated attempt by Psalm to clean it up
- Adhoc generics
- PHPStorm patchy support for advanced types
- Array/List/HashMap/ObjectLike all in one breakfast 

This library is an attempt to give the developer some kind of certainty whilst applying for Java jobs.

## Todo

- Add union types in PHP 8.0
- Add non-empty-* handling
- Add support for callables wrapping the original callable object to match the types
- Support for enums once this is added to PHP
- Add more tests around class/object-like nullable,? properties
- Rework all Cast and Match tests to deal with complete set of basic types
- Add test coverage metrics to travis, add badge
- Add types for iterable|self|static|class-string
- Add more tests for PHPDoc and Namespace resolver
- Add more tests for agreements between Psalm/PHPStan and Type assertions
