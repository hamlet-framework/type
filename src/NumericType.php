<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Union3Type<int,float,numeric-string>
 */
readonly class NumericType extends Union3Type
{
    public function __construct()
    {
        parent::__construct(new IntType, new FloatType, new NumericStringType);
    }
}
