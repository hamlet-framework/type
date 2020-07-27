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
     * @var string|null
     */
    private $sourceFieldName;

    /**
     * @param bool $successful
     * @param mixed $value
     * @psalm-param T $value
     * @param string|null $sourceFieldName
     */
    private function __construct(bool $successful, $value, $sourceFieldName)
    {
        $this->successful = $successful;
        $this->value = $value;
        $this->sourceFieldName = $sourceFieldName;
    }

    /**
     * @template Q
     * @param mixed $value
     * @psalm-param Q $value
     * @param string $sourceFieldName
     * @return self
     * @psalm-return self<Q>
     */
    public static function success($value, string $sourceFieldName): self
    {
        return new self(true, $value, $sourceFieldName);
    }

    /**
     * @return self
     * @psalm-return self<null>
     */
    public static function failure(): self
    {
        return new self(false, null, null);
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

    /**
     * @return string|null
     */
    public function sourceFieldName()
    {
        return $this->sourceFieldName;
    }
}
