<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<resource>
 */
readonly class ResourceType extends Type
{
    /**
     * @psalm-assert-if-true resource $type
     */
    public function matches(mixed $value): bool
    {
        return is_resource($value);
    }

    /**
     * @return resource
     */
    public function cast(mixed $value): mixed
    {
        if (!is_resource($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString(): string
    {
        return 'resource';
    }
}
