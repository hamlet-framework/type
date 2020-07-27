<?php declare(strict_types=1);

namespace Hamlet\Cast;

use RuntimeException;

/**
 * @template T
 * @template Q
 */
class CastException extends RuntimeException
{
    /**
     * @var mixed
     * @psalm-var T
     */
    protected $value;

    /**
     * @var Type
     * @psalm-var Type<Q>
     */
    protected $targetType;

    /**
     * @param mixed $value
     * @psalm-param T $value
     * @param Type $targetType
     * @psalm-param Type<Q> $targetType
     * @param string $details
     */
    public function __construct($value, Type $targetType, string $details = '')
    {
        $message = 'Cannot convert [' . var_export($value, true) . '] to ' . $targetType;
        if ($details) {
            $message .= '. ' . $details;
        }
        parent::__construct($message);
        $this->value = $value;
        $this->targetType = $targetType;
    }

    /**
     * @return mixed
     * @psalm-return T
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return Type
     * @psalm-return Type<Q>
     */
    public function targetType(): Type
    {
        return $this->targetType;
    }
}
