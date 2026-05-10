<?php

namespace Tests\CodeByZach\NameParser\Mapper;

use CodeByZach\NameParser\Mapper\FirstnameMapper;
use CodeByZach\NameParser\Part\Firstname;
use CodeByZach\NameParser\Part\Lastname;
use CodeByZach\NameParser\Part\Salutation;

class FirstnameMapperTest extends AbstractMapperTestCase
{
    /**
     * @return array
     */
    public static function provider()
    {
        return [
            [
                'input' => [
                    'Peter',
                    'Pan',
                ],
                'expectation' => [
                    new Firstname('Peter'),
                    'Pan',
                ],
            ],
            [
                'input' => [
                    new Salutation('Mr'),
                    'Peter',
                    'Pan',
                ],
                'expectation' => [
                    new Salutation('Mr'),
                    new Firstname('Peter'),
                    'Pan',
                ],
            ],
            [
                'input' => [
                    new Salutation('Mr'),
                    'Peter',
                    new Lastname('Pan'),
                ],
                'expectation' => [
                    new Salutation('Mr'),
                    new Firstname('Peter'),
                    new Lastname('Pan'),
                ],
            ],
            [
                'input' => [
                    'Alfonso',
                    new Salutation('Mr'),
                ],
                'expectation' => [
                    new Firstname('Alfonso'),
                    new Salutation('Mr'),
                ],
            ],
        ];
    }

    protected function getMapper()
    {
        return new FirstnameMapper();
    }
}
