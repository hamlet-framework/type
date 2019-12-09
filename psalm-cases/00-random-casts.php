<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hamlet\Cast\Type;
use function Hamlet\Cast\_bool;
use function Hamlet\Cast\_callable;
use function Hamlet\Cast\_class;
use function Hamlet\Cast\_float;
use function Hamlet\Cast\_int;
use function Hamlet\Cast\_list;
use function Hamlet\Cast\_literal;
use function Hamlet\Cast\_map;
use function Hamlet\Cast\_mixed;
use function Hamlet\Cast\_null;
use function Hamlet\Cast\_object;
use function Hamlet\Cast\_object_like;
use function Hamlet\Cast\_string;
use function Hamlet\Cast\_union;

class Example
{
    public function bool(): bool
    {
        return _bool()->cast(true);
    }

    public function callable(): callable
    {
        return _callable()->cast(function (): int {
            return 1;
        });
    }

    public function stdClass(): stdClass
    {
        return _class(stdClass::class)->cast(new stdClass());
    }

    public function float(): float
    {
        return _float()->cast(1.1);
    }

    public function int(): float
    {
        return _int()->cast(1);
    }

    /**
     * @return array<int>
     */
    public function list(): array
    {
        return _list(_int())->cast([1, 2, 3]);
    }

    /**
     * @return mixed
     * @psalm-return (1|null|'java')
     */
    public function literal()
    {
        return _literal(1, null, 'java')->cast(1);
    }

    /**
     * @return array
     * @psalm-return array<string,int>
     */
    public function map(): array
    {
        return _map(_string(), _int())->cast(['a' => 1]);
    }

    /**
     * @return mixed
     */
    public function mixed()
    {
        return _mixed()->cast(null);
    }

    /**
     * @return null
     */
    public function null()
    {
        return _null()->cast(0);
    }

    /**
     * @return object
     */
    public function object()
    {
        return _object()->cast(new stdClass());
    }

    /**
     * @return array{id:int}
     */
    public function object_like(): array
    {
        /** @var Type<array{id:int}> $type */
        $type = _object_like([
            'id' => _int()
        ]);

        return $type->cast(['id' => 1]);
    }

    public function string(): string
    {
        return _string()->cast('hello');
    }

    /**
     * @return string|null
     */
    public function union()
    {
        return _union(_string(), _null())->cast(null);
    }
}
