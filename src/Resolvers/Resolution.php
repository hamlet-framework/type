<?php

namespace Hamlet\Cast\Resolvers;

/**
 * @template T
 */
class Resolution
{
    /**
     * @var bool
     */
    private $success;

    /**
     * @var mixed
     * @psalm-var T
     */
    private $value;

    /**
     * @param bool $success
     * @param mixed $value
     * @psalm-param T $value
     */
    public function __construct(bool $success, $value)
    {
        $this->success = $success;
        $this->value = $value;
    }

    public function success(): bool
    {
        return $this->success;
    }

    /**
     * @return mixed
     * @psalm-return T
     */
    public function value()
    {
        return $this->value;
    }
}
