<?php

namespace Hamlet\Type;

class User
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Address|null */
    protected $address;

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
