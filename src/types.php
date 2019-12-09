<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @return Type<bool>
 */
function _bool(): Type
{
    return new BoolType();
}

/**
 * @template T
 * @param class-string<T> $type
 * @return Type<T>
 */
function _class(string $type): Type
{
    return new ClassType($type);
}

/**
 * @return Type<callable>
 * @psalm-suppress MixedTypeCoercion
 */
function _callable(): Type
{
    return new CallableType('callable');
}

/**
 * @return Type<float>
 */
function _float(): Type
{
    return new FloatType();
}

/**
 * @return Type
 * @psalm-return Type<int>
 */
function _int(): Type
{
    return new IntType();
}

/**
 * @template A
 * @param Type<A> $type
 * @return ListType<A>
 */
function _list(Type $type): Type
{
    return new ListType($type);
}

/**
 * @template A
 * @param array<A> $as
 * @return Type<A>
 */
function _literal(...$as): Type
{
    return new LiteralType(...$as);
}

/**
 * @template A as array-key
 * @template B
 * @param Type<A> $keyType
 * @param Type<B> $valueType
 * @return MapType<A,B>
 */
function _map(Type $keyType, Type $valueType): Type
{
    return new MapType($keyType, $valueType);
}

/**
 * @return Type<mixed>
 */
function _mixed(): Type
{
    return new MixedType();
}

/**
 * @return Type<null>
 */
function _null(): Type
{
    return new NullType();
}

/**
 * @return Type<object>
 */
function _object(): Type
{
    return new ObjectType();
}

/**
 * @template T
 * @param array<string,Type<T>> $properties
 * @return ObjectLikeType<T>
 */
function _object_like(array $properties): Type
{
    return new ObjectLikeType($properties);
}

/**
 * @return Type<resource>
 */
function _resource(): Type
{
    return new ResourceType();
}

/**
 * @return Type<string>
 */
function _string(): Type
{
    return new StringType();
}

/**
 * @template A
 * @param Type<A> ...$as
 * @return Type<A>
 */
function _union(Type ...$as): Type
{
    return new UnionType(...$as);
}
