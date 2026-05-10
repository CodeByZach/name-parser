<?php

namespace CodeByZach\NameParser\Part;

class Nickname extends AbstractPart
{
    /**
     * camelcase the nickname for normalization
     */
    public function normalize(): string
    {
        return $this->camelcase($this->getValue());
    }
}
