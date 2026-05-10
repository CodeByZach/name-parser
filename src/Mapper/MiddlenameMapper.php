<?php

namespace CodeByZach\NameParser\Mapper;

use CodeByZach\NameParser\Part\AbstractPart;
use CodeByZach\NameParser\Part\Firstname;
use CodeByZach\NameParser\Part\Lastname;
use CodeByZach\NameParser\Part\Middlename;

class MiddlenameMapper extends AbstractMapper
{
    public function __construct(
        protected bool $mapWithoutLastname = false,
    ) {}

    /**
     * map middlenames in the parts array
     *
     * @param  array<int, AbstractPart|string>  $parts
     * @return array<int, AbstractPart|string>
     */
    #[\Override]
    public function map(array $parts): array
    {
        // If we don't expect a lastname, match a mimimum of 2 parts
        $minumumParts = ($this->mapWithoutLastname ? 2 : 3);

        if (count($parts) < $minumumParts) {
            return $parts;
        }

        $start = $this->findFirstMapped(Firstname::class, $parts);

        if ($start === false) {
            return $parts;
        }

        return $this->mapFrom($start, $parts);
    }

    /**
     * @param  array<int, AbstractPart|string>  $parts
     * @return array<int, AbstractPart|string>
     */
    protected function mapFrom(int $start, array $parts): array
    {
        // If we don't expect a lastname, include the last part,
        // otherwise skip the last (-1) because it should be a lastname
        $length = count($parts) - ($this->mapWithoutLastname ? 0 : 1);

        for ($k = $start; $k < $length; $k++) {
            $part = $parts[$k];

            if ($part instanceof Lastname) {
                break;
            }

            if ($part instanceof AbstractPart) {
                continue;
            }

            $parts[$k] = new Middlename($part);
        }

        return $parts;
    }
}
