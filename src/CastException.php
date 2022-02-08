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
     * @var T
     */
    protected mixed $value;

    /**
     * @var Type<Q>
     */
    protected Type $targetType;

    /**
     * @param T $value
     * @param Type<Q> $targetType
     * @param string $details
     */
    public function __construct(mixed $value, Type $targetType, string $details = '')
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
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * @return Type<Q>
     */
    public function targetType(): Type
    {
        return $this->targetType;
    }
}
