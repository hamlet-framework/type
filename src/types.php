<?php

namespace Hamlet\Cast;

function _int(): IntType
{
    return new IntType();
}

function _bool(): BoolType
{
    return new BoolType();
}

function _float(): FloatType
{
    return new FloatType();
}

function _string(): StringType
{
    return new StringType();
}

function _null(): NullType
{
    return new NullType();
}

function _mixed(): MixedType
{
    return new MixedType();
}

/**
 * @template A
 * @param Type[] $as
 * @psalm-param array<Type<A>> $as
 * @return UnionType
 * @psalm-return UnionType<A>
 */
function _union(Type ...$as): UnionType
{
    return new UnionType(...$as);
}

/**
 * @template A
 * @param array $as
 * @psalm-param array<A> $as
 * @return LiteralType
 * @psalm-return LiteralType<A>
 */
function _literal(...$as): LiteralType
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
 * @return MapType
 * @psalm-return MapType<A,B>
 */
function _map(Type $keyType, Type $valueType): MapType
{
    return new MapType($keyType, $valueType);
}

/**
 * @template A
 * @param Type $type
 * @psalm-param Type<A> $type
 * @return ListType
 * @psalm-return ListType<A>
 */
function _list(Type $type): ListType
{
    return new ListType($type);
}

/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @return ClassType
 * @psalm-return ClassType<T>
 */
function _class(string $type): ClassType
{
    return new ClassType($type);
}

/**
 * @template N as string
 * @template T
 * @param string $name
 * @psalm-param N $name
 * @param bool $required
 * @param Type $type
 * @psalm-param Type<T> $type
 * @return PropertyType
 * @psalm-return PropertyType<N,T>
 */
function _property(string $name, bool $required, Type $type): PropertyType
{
    return new PropertyType($name, $required, $type);
}

/**
 * @template T
 * @template S
 * @param Type $type
 * @psalm-param Type<T> $type
 * @param Type...$types
 * @psalm-param array<Type<S>> $types
 * @return Type
 * @psalm-return Type<T&S>|Type<T>
 * @psalm-suppress InvalidDocblock
 */
function _intersection(Type $type, Type ...$types): Type
{
    if (empty($types)) {
        return $type;
    }
    return new IntersectionType($type, _intersection(...$types));
}
