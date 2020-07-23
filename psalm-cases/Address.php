<?php

namespace Hamlet\Cast;

class Address
{
    /** @var string */
    private $city;

    public function city(): string
    {
        return $this->city;
    }
}
