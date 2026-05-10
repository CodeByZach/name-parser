<?php

namespace CodeByZach\NameParser\Mapper;

use CodeByZach\NameParser\Part\AbstractPart;
use CodeByZach\NameParser\Part\Firstname;
use CodeByZach\NameParser\Part\Initial;
use CodeByZach\NameParser\Part\Lastname;
use CodeByZach\NameParser\Part\Salutation;

class FirstnameMapper extends AbstractMapper
{
    /**
     * map firstnames in parts array
     *
     * @param  array  $parts  the parts
     * @return array the mapped parts
     */
    #[\Override]
    public function map(array $parts): array
    {
        if (count($parts) < 2) {
            return [$this->handleSinglePart($parts[0])];
        }

        $pos = $this->findFirstnamePosition($parts);

        if ($pos !== null) {
            $parts[$pos] = new Firstname($parts[$pos]);
        }

        return $parts;
    }

    /**
     * @param  string|AbstractPart  $part
     */
    protected function handleSinglePart($part): AbstractPart
    {
        if ($part instanceof AbstractPart) {
            return $part;
        }

        return new Firstname($part);
    }

    protected function findFirstnamePosition(array $parts): ?int
    {
        $pos = null;

        $length = count($parts);
        $start = $this->getStartIndex($parts);

        for ($k = $start; $k < $length; $k++) {
            $part = $parts[$k];

            if ($part instanceof Lastname) {
                break;
            }

            if ($part instanceof Initial && $pos === null) {
                $pos = $k;
            }

            if ($part instanceof AbstractPart) {
                continue;
            }

            return $k;
        }

        return $pos;
    }

    protected function getStartIndex(array $parts): int
    {
        $index = $this->findFirstMapped(Salutation::class, $parts);

        if ($index === false) {
            return 0;
        }

        if ($index === count($parts) - 1) {
            return 0;
        }

        return $index + 1;
    }
}
