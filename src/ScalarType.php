<?php

namespace Hamlet\Cast;

/**
 * @extends UnionType<int|bool|float|string>
 */
class ScalarType extends UnionType
{
    public function __construct()
    {
        parent::__construct(new IntType, new BoolType, new FloatType, new StringType);
    }
}
