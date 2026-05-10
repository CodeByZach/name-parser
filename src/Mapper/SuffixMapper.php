<?php

namespace CodeByZach\NameParser\Mapper;

use CodeByZach\NameParser\Part\AbstractPart;
use CodeByZach\NameParser\Part\Suffix;

class SuffixMapper extends AbstractMapper
{
    /**
     * @param  array<string, string>  $suffixes
     */
    public function __construct(
        protected array $suffixes,
        protected bool $matchSinglePart = false,
        protected int $reservedParts = 2,
    ) {}

    /**
     * map suffixes in the parts array
     *
     * @param  array<int, AbstractPart|string>  $parts
     * @return array<int, AbstractPart|string>
     */
    #[\Override]
    public function map(array $parts): array
    {
        if ($this->isMatchingSinglePart($parts)) {
            $first = $parts[0];
            if (is_string($first)) {
                $parts[0] = new Suffix($first, $this->suffixes[$this->getKey($first)]);
            }

            return $parts;
        }

        $start = count($parts) - 1;

        for ($k = $start; $k > $this->reservedParts - 1; $k--) {
            $part = $parts[$k];

            if (! $this->isSuffix($part)) {
                break;
            }

            // isSuffix() guarantees $part is a string at this point
            $parts[$k] = new Suffix($part, $this->suffixes[$this->getKey($part)]);
        }

        return $parts;
    }

    /**
     * @param  array<int, AbstractPart|string>  $parts
     */
    protected function isMatchingSinglePart(array $parts): bool
    {
        if (! $this->matchSinglePart) {
            return false;
        }

        if (count($parts) !== 1) {
            return false;
        }

        return $this->isSuffix($parts[0]);
    }

    /**
     * @phpstan-assert-if-true string $part
     */
    protected function isSuffix(AbstractPart|string $part): bool
    {
        if ($part instanceof AbstractPart) {
            return false;
        }

        return array_key_exists($this->getKey($part), $this->suffixes);
    }
}
