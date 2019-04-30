<?php

namespace Hamlet\Cast;

use RuntimeException;

class CastException extends RuntimeException
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var Type
     */
    private $targetType;

    /**
     * @param mixed $value
     * @param Type $targetType
     */
    public function __construct($value, Type $targetType)
    {
        parent::__construct('Cannot convert ' . var_export($value, true) . ' to ' . $targetType);
        $this->value = $value;
        $this->targetType = $targetType;
    }
}
