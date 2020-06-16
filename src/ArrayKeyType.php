<?php declare(strict_types=1);

namespace Hamlet\Type;

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
