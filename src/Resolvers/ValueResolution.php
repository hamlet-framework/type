<?php declare(strict_types=1);

namespace Hamlet\Cast\Resolvers;

/**
 * @template T
 */
final class ValueResolution
{
    /**
     * @var bool
     */
    private $successful;

    /**
     * @var mixed
     * @psalm-var T
     */
    private $value;

    /**
     * @param bool $successful
     * @param mixed $value
     * @psalm-param T $value
     */
    private function __construct(bool $successful, $value)
    {
        $this->successful = $successful;
        $this->value = $value;
    }

    /**
     * @template Q
     * @param mixed $value
     * @psalm-param Q $value
     * @return self
     * @psalm-return self<Q>
     */
    public static function success($value): self
    {
        return new self(true, $value);
    }

    /**
     * @return self
     * @psalm-return self<null>
     */
    public static function failure(): self
    {
        return new self(false, null);
    }

    public function successful(): bool
    {
        return $this->successful;
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
