<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Union4Type<int,bool,float,string>
 */
readonly class ScalarType extends Union4Type
{
    public function __construct()
    {
        parent::__construct(new IntType, new BoolType, new FloatType, new StringType);
    }
}
