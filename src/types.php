<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @return BoolType
 * @psalm-return Type<bool>
 */
function _bool(): Type
{
    return new BoolType();
}

/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @return ClassType
 * @psalm-return Type<T>
 */
function _class(string $type): Type
{
    return new ClassType($type);
}

/**
 * @return CallableType
 * @psalm-return Type<callable>
 */
function _callable(): Type
{
    return new CallableType('callable');
}

/**
 * @return FloatType
 * @pslam-return Type<float>
 */
function _float(): Type
{
    return new FloatType();
}

/**
 * @return IntType
 * @psalm-return Type<int>
 */
function _int(): Type
{
    return new IntType();
}

/**
 * @template A
 * @param Type $type
 * @psalm-param Type<A> $type
 * @return ListType
 * @psalm-return Type<list<A>>
 *
 * @psalm-suppress InvalidReturnType
 * @psalm-suppress InvalidReturnStatement
 */
function _list(Type $type): Type
{
    return new ListType($type);
}

/**
 * @template A
 * @param array $as
 * @psalm-param array<A> $as
 * @return LiteralType
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
 * @return MapType
 * @psalm-return Type<array<A,B>>
 *
 * @psalm-suppress InvalidReturnType
 * @psalm-suppress InvalidReturnStatement
 */
function _map(Type $keyType, Type $valueType): Type
{
    return new MapType($keyType, $valueType);
}

/**
 * @return MixedType
 * @psalm-return Type<mixed>
 */
function _mixed(): Type
{
    return new MixedType();
}

/**
 * @return NullType
 * @psalm-return Type<null>
 */
function _null(): Type
{
    return new NullType();
}

/**
 * @return ObjectType
 * @pslam-return Type<object>
 */
function _object(): Type
{
    return new ObjectType();
}

/**
 * @template T
 * @param Type[] $properties
 * @psalm-param array<string,Type<T>> $properties
 * @return ObjectLikeType
 * @psalm-return ObjectLikeType<T>
 */
function _object_like(array $properties): Type
{
    return new ObjectLikeType($properties);
}

/**
 * @return ResourceType
 * @psalm-return Type<resource>
 */
function _resource(): Type
{
    return new ResourceType();
}

/**
 * @return StringType
 * @psalm-return Type<string>
 */
function _string(): Type
{
    return new StringType();
}

/**
 * @template A
 * @param Type[] $as
 * @psalm-param Type<A> ...$as
 * @return UnionType
 * @psalm-return Type<A>
 */
function _union(Type ...$as): Type
{
    return new UnionType(...$as);
}
