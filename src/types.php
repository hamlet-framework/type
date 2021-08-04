<?php declare(strict_types=1);

namespace Hamlet\Cast;

use RuntimeException;

/**
 * @return BoolType
 * @psalm-return Type<bool>
 * @psalm-suppress InvalidReturnType
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
 * @psalm-suppress InvalidReturnType
 */
function _class(string $type): Type
{
    return new ClassType($type);
}

/**
 * @return CallableType
 * @psalm-return Type<callable>
 * @psalm-suppress InvalidReturnType
 */
function _callable(): Type
{
    return new CallableType('callable');
}

/**
 * @return FloatType
 * @pslam-return Type<float>
 * @psalm-suppress InvalidReturnType
 */
function _float(): Type
{
    return new FloatType();
}

/**
 * @return IntType
 * @psalm-return Type<int>
 * @psalm-suppress InvalidReturnType
 */
function _int(): Type
{
    return new IntType();
}

/**
 * @return NumericStringType
 * @psalm-return Type<numeric-string>
 * @psalm-suppress InvalidReturnType
 */
function _numeric_string(): Type
{
    return new NumericStringType;
}

/**
 * @return NumericType
 * @psalm-return Type<numeric>
 * @psalm-suppress InvalidReturnStatement
 * @psalm-suppress InvalidReturnType
 */
function _numeric(): Type
{
    return new NumericType;
}

/**
 * @return ScalarType
 * @psalm-return Type<scalar>
 * @psalm-suppress InvalidReturnStatement
 * @psalm-suppress InvalidReturnType
 */
function _scalar(): Type
{
    return new ScalarType;
}

/**
 * @return ArrayKeyType
 * @psalm-return Type<array-key>
 * @psalm-suppress InvalidReturnType
 */
function _array_key(): Type
{
    return new ArrayKeyType;
}

/**
 * @template A
 * @param Type $type
 * @psalm-param Type<A> $type
 * @return ListType
 * @psalm-return Type<list<A>>
 */
function _list(Type $type): Type
{
    return new ListType($type);
}

/**
 * @template A
 * @param Type $type
 * @psalm-param Type<A> $type
 * @return ArrayType
 * @psalm-return Type<array<A>>
 */
function _array(Type $type): Type
{
    return new ArrayType($type);
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
 * @psalm-suppress InvalidReturnType
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
 * @psalm-suppress InvalidReturnType
 */
function _object_like(array $properties): Type
{
    return new ObjectLikeType($properties);
}

/**
 * @return ResourceType
 * @psalm-return Type<resource>
 * @psalm-suppress InvalidReturnType
 */
function _resource(): Type
{
    return new ResourceType();
}

/**
 * @return StringType
 * @psalm-return Type<string>
 * @psalm-suppress InvalidReturnType
 */
function _string(): Type
{
    return new StringType();
}

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @template F
 * @template G
 * @template H
 * @param Type $a
 * @param Type $b
 * @param Type|null $c
 * @param Type|null $d
 * @param Type|null $e
 * @param Type|null $f
 * @param Type|null $g
 * @param Type|null $h
 * @psalm-param Type<A> $a
 * @psalm-param Type<B> $b
 * @psalm-param Type<C>|null $c
 * @psalm-param Type<D>|null $d
 * @psalm-param Type<E>|null $e
 * @psalm-param Type<F>|null $f
 * @psalm-param Type<G>|null $g
 * @psalm-param Type<H>|null $h
 * @return Type
 * @psalm-return (func_num_args() is 2 ? Type<A|B> : (func_num_args() is 3 ? Type<A|B|C> : (func_num_args() is 4 ? Type<A|B|C|D> : (func_num_args() is 5 ? Type<A|B|C|D|E> : (func_num_args() is 6 ? Type<A|B|C|D|E|F> : (func_num_args() is 7 ? Type<A|B|C|D|E|F|G> : Type<A|B|C|D|E|F|G|H>))))))
 * @psalm-suppress InvalidReturnType
 */
function _union(Type $a, Type $b, Type $c = null, Type $d = null, Type $e = null, Type $f = null, Type $g = null, Type $h = null): Type
{
    switch (func_num_args()) {
        case 2:
            return new Union2Type($a, $b);
        case 3:
            if ($c === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union3Type($a, $b, $c);
        case 4:
            if ($c === null || $d === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union4Type($a, $b, $c, $d);
        case 5:
            if ($c === null || $d === null || $e === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union5Type($a, $b, $c, $d, $e);
        case 6:
            if ($c === null || $d === null || $e === null || $f === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union6Type($a, $b, $c, $d, $e, $f);
        case 7:
            if ($c === null || $d === null || $e === null || $f === null || $g === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union7Type($a, $b, $c, $d, $e, $f, $g);
        case 8:
            if ($c === null || $d === null || $e === null || $f === null || $g === null || $h === null) {
                throw new RuntimeException('Type cannot be null');
            }
            return new Union8Type($a, $b, $c, $d, $e, $f, $g, $h);
        default:
            throw new RuntimeException('Unsupported number of arguments');
    }
}
