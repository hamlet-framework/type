Hamlet Cast 
===

There are few aspects of specifying type of an expression in PHP:

1. The most exact specification of the type (we assume it's in psalm syntax), for example `array<int,DateTime|null>`
2. The sequence of assertions that are required to check that the actual object at run time has this type, in pseudo code something like `assert($records instanceof array<int,DateTime|null>)`
3. The hint that will tell static analysers (this is meant to be psalm at the moment) what's the exact type specification after the cast, or in pseudo code`(array<int,DateTime|null>) $records`
4. The ability of deriving the types from other types without doing string manipulation, expressed in pseudo code as something like `array<int,DateTime|null> || null`
5. The ability to cast to it when the casting is safe, i.e. falsey should cast to false, etc.

This library provides the basic building blocks for type specifications. For example the following expression

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

This object can be used in following ways

Assert at run time that the type of `$records` is `array<int,DateTime|null>>`:
```php
assert($type($records));
```

Cast `$records` to `array<int,DateTime|null>>` and throw an exception when `$records` are not of type `array<int,DateTime|null>>` when assertions are enabled:
```php
return $type->cast($records);
```

Cast `$records` to `array<int,DateTime|null>>` or throw a runtime exception if cast fails:
```php
return $type->castOrFail($records);
```

Combine type with other types, for example, making it nullable `array<int,DateTime|null>>|null`:
```php
_union($type, _null())
```

## Object like arrays

Object like arrays are specified as intersections of simpler property types. For example for type `array{id:int,name:string,valid?:bool}` 
corresponds to this construct:

```php
$type = _intersection(
    _property('id', true, _int()),
    _property('name', true, _string()),
    _property('valid', false, _bool())
);
``` 

Quite a mouthful. But it allows us to do things like this

## Type casting PHP

`bool` - can be converted from any type
``
