<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Union2Type<int,string>
 */
class ArrayKeyType extends Union2Type
{
    public function __construct()
    {
        parent::__construct(new IntType, new StringType);
    }
}
