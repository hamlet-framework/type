<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends UnionType<scalar>
 */
class ScalarType extends UnionType
{
    public function __construct()
    {
        parent::__construct(new IntType, new BoolType, new FloatType, new StringType);
    }
}
