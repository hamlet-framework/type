<?php declare(strict_types=1);

namespace Hamlet\Cast\Resolvers;

/**
 * @template T
 */
final class ValueResolution
{
    /**
     * @param T $value
     */
    private function __construct(private bool $successful, private mixed $value, private ?string $sourceFieldName)
    {
    }

    /**
     * @template Q
     * @param Q $value
     * @param string $sourceFieldName
     * @return self<Q>
     */
    public static function success(mixed $value, string $sourceFieldName): self
    {
        return new self(true, $value, $sourceFieldName);
    }

    /**
     * @return self<null>
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
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }

    public function sourceFieldName(): ?string
    {
        return $this->sourceFieldName;
    }
}
