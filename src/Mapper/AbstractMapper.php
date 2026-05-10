<?php

namespace CodeByZach\NameParser\Mapper;

use CodeByZach\NameParser\Part\AbstractPart;

abstract class AbstractMapper
{
    /**
     * implements the mapping of parts
     *
     * @param  array<int, AbstractPart|string>  $parts
     * @return array<int, AbstractPart|string>
     */
    abstract public function map(array $parts): array;

    /**
     * checks if there are still unmapped parts left before the given position
     *
     * @param  array<int, AbstractPart|string>  $parts
     */
    protected function hasUnmappedPartsBefore(array $parts, int $index): bool
    {
        foreach ($parts as $k => $part) {
            if ($k === $index) {
                break;
            }

            if (! ($part instanceof AbstractPart)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  class-string  $type
     * @param  array<int, AbstractPart|string>  $parts
     */
    protected function findFirstMapped(string $type, array $parts): int|false
    {
        $total = count($parts);

        for ($i = 0; $i < $total; $i++) {
            if ($parts[$i] instanceof $type) {
                return $i;
            }
        }

        return false;
    }

    /**
     * get the registry lookup key for the given word
     */
    protected function getKey(string $word): string
    {
        return strtolower(str_replace('.', '', $word));
    }
}
