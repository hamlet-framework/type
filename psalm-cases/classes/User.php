<?php

namespace Hamlet\Cast;

class User
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var Address|null */
    private $address;

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function address()
    {
        return $this->address;
    }
}
