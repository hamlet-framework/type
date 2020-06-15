<?php

namespace Hamlet\Cast;

/**
 * @extends UnionType<int|string>
 */
class ArrayKeyType extends UnionType
{
    public function __construct()
    {
        parent::__construct(new IntType, new StringType);
    }
}
