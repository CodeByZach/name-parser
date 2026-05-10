<?php

namespace CodeByZach\NameParser\Part;

class Initial extends GivenNamePart
{
    /**
     * uppercase the initial
     */
    public function normalize(): string
    {
        return strtoupper($this->getValue());
    }
}
