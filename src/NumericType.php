<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends UnionType<int|float|numeric-string>
 */
class NumericType extends UnionType
{
    public function __construct()
    {
        parent::__construct(new IntType, new FloatType, new NumericStringType);
    }
}
