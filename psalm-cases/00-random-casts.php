<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Hamlet\Type\_bool;
use function Hamlet\Type\_class;
use function Hamlet\Type\_float;
use function Hamlet\Type\_int;
use function Hamlet\Type\_list;
use function Hamlet\Type\_literal;
use function Hamlet\Type\_map;
use function Hamlet\Type\_mixed;
use function Hamlet\Type\_null;
use function Hamlet\Type\_object;
use function Hamlet\Type\_string;
use function Hamlet\Type\_union;

class Example
{
    public function bool(): bool
    {
        return _bool()->cast(true);
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
     * @return (1|null|'java')
     */
    public function literal(): mixed
    {
        return _literal(1, null, 'java')->cast(1);
    }

    /**
     * @return array<string,int>
     */
    public function map(): array
    {
        return _map(_string(), _int())->cast(['a' => 1]);
    }

    public function mixed(): mixed
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

    public function object(): object
    {
        return _object()->cast(new stdClass());
    }

    public function string(): string
    {
        return _string()->cast('hello');
    }

    public function union(): ?string
    {
        return _union(_string(), _null())->cast(null);
    }
}
