<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Parser\DeclarationReader;
use InvalidArgumentException;
use PhpParser\NameContext;

function type_of(string $declaration, ?NameContext $nameContext = null): Type
{
    return match ($declaration) {
        'string'    => new Types\StringType,
        'int'       => new Types\IntType,
        'float'     => new Types\FloatType,
        'bool'      => new Types\BoolType,
        'mixed'     => new Types\MixedType,
        'resource'  => new Types\ResourceType,
        'null'      => new Types\NullType,
        default     => DeclarationReader::instance()->read($declaration, $nameContext),
    };
}

/**
 * @return Type<mixed>
 */
function _mixed(): Type
{
    return new Types\MixedType;
}

/**
 * @return Type<int>
 */
function _int(): Type
{
    return new Types\IntType;
}

/**
 * @return Type<float>
 */
function _float(): Type
{
    return new Types\FloatType;
}

/**
 * @return Type<string>
 */
function _string(): Type
{
    return new Types\StringType;
}

/**
 * @return Type<non-empty-string>
 */
function _non_empty_string(): Type
{
    return new Types\NonEmptyStringType;
}

/**
 * @return Type<numeric-string>
 */
function _numeric_string(): Type
{
    return new Types\NumericStringType;
}

/**
 * @return Type<bool>
 */
function _bool(): Type
{
    return new Types\BoolType;
}

/**
 * @return Type<numeric>
 */
function _numeric(): Type
{
    return new Types\NumericType;
}

/**
 * @return Type<scalar>
 */
function _scalar(): Type
{
    return new Types\ScalarType;
}

/**
 * @return Type<array-key>
 */
function _array_key(): Type
{
    return new Types\ArrayKeyType;
}

/**
 * @return Type<null>
 */
function _null(): Type
{
    return new Types\NullType;
}

/**
 * // @todo literal types should be checked, cannot be just anything.
 * @template A of scalar|null
 * @param array<A> $as
 * @return Type<A>
 */
function _literal(...$as): Type
{
    return new Types\LiteralType(...$as);
}

/**
 * @return Type<object>
 */
function _object(): Type
{
    return new Types\ObjectType;
}

/**
 * @return Type<resource>
 */
function _resource(): Type
{
    return new Types\ResourceType;
}

/**
 * @template T
 * @param class-string<T> $type
 * @return Type<T>
 */
function _class(string $type): Type
{
    return new Types\ClassType($type);
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
 * @param Type<A> $a
 * @param Type<B> $b
 * @param Type<C>|null $c
 * @param Type<D>|null $d
 * @param Type<E>|null $e
 * @param Type<F>|null $f
 * @param Type<G>|null $g
 * @param Type<H>|null $h
 * @return Type
 * @psalm-return (func_num_args() is 2 ? Type<A|B> : (func_num_args() is 3 ? Type<A|B|C> : (func_num_args() is 4 ? Type<A|B|C|D> : (func_num_args() is 5 ? Type<A|B|C|D|E> : (func_num_args() is 6 ? Type<A|B|C|D|E|F> : (func_num_args() is 7 ? Type<A|B|C|D|E|F|G> : Type<A|B|C|D|E|F|G|H>))))))
 * @phpstan-return ($h is Type ? Type<A|B|C|D|E|F|G|H> : ($g is Type ? Type<A|B|C|D|E|F|G> : ($f is Type ? Type<A|B|C|D|E|F> : ($e is Type ? Type<A|B|C|D|E> : ($d is Type ? Type<A|B|C|D> : ($c is Type ? Type<A|B|C> : Type<A|B>))))))
 * @psalm-suppress MixedReturnTypeCoercion
 */
function _union(Type $a, Type $b, Type $c = null, Type $d = null, Type $e = null, Type $f = null, Type $g = null, Type $h = null): Type
{
    $args = func_get_args();
    $options = [];
    if (count($args) > 8) {
        throw new InvalidArgumentException('At most 8 elements');
    }
    /**
     * @psalm-suppress MixedAssignment
     */
    foreach ($args as $arg) {
        if (!$arg instanceof Type) {
            throw new InvalidArgumentException('Invalid argument');
        } else {
            $options[] = $arg;
        }
    }
    return new Types\UnionType($options);
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
 * @param Type<A> $a
 * @param Type<B> $b
 * @param Type<C>|null $c
 * @param Type<D>|null $d
 * @param Type<E>|null $e
 * @param Type<F>|null $f
 * @param Type<G>|null $g
 * @param Type<H>|null $h
 * @return Type
 * @psalm-return (func_num_args() is 2 ? Type<list{A,B}> : (func_num_args() is 3 ? Type<list{A,B,C}> : (func_num_args() is 4 ? Type<list{A,B,C,D}> : (func_num_args() is 5 ? Type<list{A,B,C,D,E}> : (func_num_args() is 6 ? Type<list{A,B,C,D,E,F}> : (func_num_args() is 7 ? Type<list{A,B,C,D,E,F,G}> : Type<list{A,B,C,D,E,F,G,H}>))))))
 * @phpstan-return ($h is Type ? Type<list{A,B,C,D,E,F,G,H}> : ($g is Type ? Type<list{A,B,C,D,E,F,G}> : ($f is Type ? Type<list{A,B,C,D,E,F}> : ($e is Type ? Type<list{A,B,C,D,E}> : ($d is Type ? Type<list{A,B,C,D}> : ($c is Type ? Type<list{A,B,C}> : Type<list{A,B}>))))))
 * @psalm-suppress InvalidReturnType
 * @psalm-suppress MixedReturnTypeCoercion
 */
function _tuple(Type $a, Type $b, Type $c = null, Type $d = null, Type $e = null, Type $f = null, Type $g = null, Type $h = null): Type
{
    $args = func_get_args();
    $fields = [];
    if (count($args) > 8) {
        throw new InvalidArgumentException('At most 8 elements');
    }
    /**
     * @psalm-suppress MixedAssignment
     */
    foreach ($args as $arg) {
        if (!$arg instanceof Type) {
            throw new InvalidArgumentException('Invalid argument');
        } else {
            $fields[] = $arg;
        }
    }
    /**
     * @psalm-suppress InvalidReturnStatement
     */
    return new Types\TupleType($fields);
}

/**
 * @template A
 * @param Type<A> $elementTime
 * @return Type<array<A>>
 */
function _array(Type $elementTime): Type
{
    return new Types\ArrayType($elementTime);
}

/**
 * @template A
 * @param Type<A> $elementType
 * @return Type<non-empty-array<A>>
 */
function _non_empty_array(Type $elementType): Type
{
    return new Types\NonEmptyArrayType($elementType);
}

/**
 * @template A
 * @param Type<A> $elementType
 * @return Type<list<A>>
 */
function _list(Type $elementType): Type
{
    return new Types\ListType($elementType);
}

/**
 * @template A
 * @param Type<A> $elementType
 * @return Type<non-empty-list<A>>
 */
function _non_empty_list(Type $elementType): Type
{
    return new Types\NonEmptyListType($elementType);
}

/**
 * @template A as array-key
 * @template B
 * @param Type<A> $keyType
 * @param Type<B> $valueType
 * @return Type<array<A,B>>
 */
function _map(Type $keyType, Type $valueType): Type
{
    return new Types\MapType($keyType, $valueType);
}
