<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Union3Type<int,float,numeric-string>
 */
class NumericType extends Union3Type
{
    public function __construct()
    {
        parent::__construct(new IntType, new FloatType, new NumericStringType);
    }
}
