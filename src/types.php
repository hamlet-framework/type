<?php

namespace Hamlet\Cast;

/**
 * @return Type
 * @psalm-return Type<int>
 */
function _int(): Type
{
    return new IntType();
}

/**
 * @return Type
 * @psalm-return Type<bool>
 */
function _bool(): Type
{
    return new BoolType();
}

/**
 * @return Type
 * @psalm-return Type<float>
 */
function _float(): Type
{
    return new FloatType();
}

/**
 * @return Type
 * @psalm-return Type<string>
 */
function _string(): Type
{
    return new StringType();
}

/**
 * @return Type
 * @psalm-return Type<null>
 */
function _null(): Type
{
    return new NullType();
}

/**
 * @return Type
 * @psalm-return Type<object>
 */
function _object(): Type
{
    return new ObjectType();
}

/**
 * @return Type
 * @psalm-return Type<mixed>
 */
function _mixed(): Type
{
    return new MixedType();
}

/**
 * @template A
 * @param Type[] $as
 * @psalm-param array<Type<A>> $as
 * @return Type
 * @psalm-return Type<A>
 */
function _union(Type ...$as): Type
{
    return new UnionType(...$as);
}

/**
 * @template A
 * @param array $as
 * @psalm-param array<A> $as
 * @return Type
 * @psalm-return Type<A>
 */
function _literal(...$as): Type
{
    return new LiteralType(...$as);
}

/**
 * @template A as array-key
 * @template B
 * @param Type $keyType
 * @psalm-param Type<A> $keyType
 * @param Type $valueType
 * @psalm-param Type<B> $valueType
 * @return Type
 * @psalm-return Type<array<A,B>>
 */
function _map(Type $keyType, Type $valueType): Type
{
    return new MapType($keyType, $valueType);
}

/**
 * @template A
 * @param Type $type
 * @psalm-param Type<A> $type
 * @return Type
 * @psalm-return Type<array<array-key,A>>
 */
function _list(Type $type): Type
{
    return new ListType($type);
}

/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @return Type
 * @psalm-return Type<T>
 */
function _class(string $type): Type
{
    return new ClassType($type);
}

/**
 * @template N as int|string|null
 * @template T
 * @param int|string|null $name
 * @psalm-param N $name
 * @param bool $required
 * @param Type $type
 * @psalm-param Type<T> $type
 * @return PropertyType
 * @psalm-return PropertyType<N,T>
 */
function _property($name, bool $required, Type $type): PropertyType
{
    return new PropertyType($name, $required, $type);
}

/**
 * @template T
 * @template S
 * @param Type $type
 * @psalm-param Type<T> $type
 * @param Type ...$types
 * @psalm-param array<Type<S>> $types
 * @return Type
 * @psalm-return Type<T&S>|Type<T>
 */
function _intersection(Type $type, Type ...$types): Type
{
    if (empty($types)) {
        return $type;
    }
    return new IntersectionType($type, _intersection(...$types));
}
