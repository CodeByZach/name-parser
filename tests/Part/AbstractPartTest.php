<?php

namespace Tests\CodeByZach\NameParser\Part;

use CodeByZach\NameParser\Part\AbstractPart;
use PHPUnit\Framework\TestCase;

class AbstractPartTest extends TestCase
{
    public function testNormalize()
    {
        $part = new class('abc') extends AbstractPart {};
        $this->assertEquals('abc', $part->normalize());
    }

    public function testSetValueUnwraps()
    {
        $part = new class('abc') extends AbstractPart {};
        $this->assertEquals('abc', $part->getValue());

        $wrapped = new class($part) extends AbstractPart {};
        $this->assertEquals('abc', $wrapped->getValue());
    }
}
