<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<resource>
 */
readonly class ResourceType extends Type
{
    /**
     * @psalm-assert-if-true resource $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return is_resource($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        throw new CastException($value, $this);
    }

    #[Override] public function __toString(): string
    {
        return 'resource';
    }
}
