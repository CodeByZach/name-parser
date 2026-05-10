<?php

namespace CodeByZach\NameParser;

use CodeByZach\NameParser\Language\English;
use CodeByZach\NameParser\Mapper\FirstnameMapper;
use CodeByZach\NameParser\Mapper\InitialMapper;
use CodeByZach\NameParser\Mapper\LastnameMapper;
use CodeByZach\NameParser\Mapper\MiddlenameMapper;
use CodeByZach\NameParser\Mapper\NicknameMapper;
use CodeByZach\NameParser\Mapper\SalutationMapper;
use CodeByZach\NameParser\Mapper\SuffixMapper;

class Parser
{
    protected string $whitespace = " \r\n\t";

    /**
     * @var array<int, \CodeByZach\NameParser\Mapper\AbstractMapper>
     */
    protected array $mappers = [];

    /**
     * @var array<int, LanguageInterface>
     */
    protected array $languages = [];

    /**
     * @var array<string, string>
     */
    protected array $nicknameDelimiters = [];

    protected int $maxSalutationIndex = 0;

    protected int $maxCombinedInitials = 2;

    /**
     * @param  array<int, LanguageInterface>  $languages
     */
    public function __construct(array $languages = [])
    {
        if (empty($languages)) {
            $languages = [new English()];
        }

        $this->languages = $languages;
    }

    /**
     * split full names into the following parts:
     * - prefix / salutation  (Mr., Mrs., etc)
     * - given name / first name
     * - middle initials
     * - surname / last name
     * - suffix (II, Phd, Jr, etc)
     */
    public function parse(string $name): Name
    {
        $name = $this->normalize($name);

        $segments = explode(',', $name);

        if (count($segments) > 1) {
            return $this->parseSplitName($segments[0], $segments[1], $segments[2] ?? '');
        }

        $parts = explode(' ', $name);

        foreach ($this->getMappers() as $mapper) {
            $parts = $mapper->map($parts);
        }

        return new Name($parts);
    }

    /**
     * handles split-parsing of comma-separated name parts
     */
    protected function parseSplitName(string $first, string $second, string $third): Name
    {
        $parts = array_merge(
            $this->getFirstSegmentParser()->parse($first)->getParts(),
            $this->getSecondSegmentParser()->parse($second)->getParts(),
            $this->getThirdSegmentParser()->parse($third)->getParts()
        );

        return new Name($parts);
    }

    protected function getFirstSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new SalutationMapper($this->getSalutations(), $this->getMaxSalutationIndex()),
            new SuffixMapper($this->getSuffixes(), false, 2),
            new LastnameMapper($this->getPrefixes(), true),
            new FirstnameMapper(),
            new MiddlenameMapper(),
        ]);

        return $parser;
    }

    protected function getSecondSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new SalutationMapper($this->getSalutations(), $this->getMaxSalutationIndex()),
            new SuffixMapper($this->getSuffixes(), true, 1),
            new NicknameMapper($this->getNicknameDelimiters()),
            new InitialMapper($this->getMaxCombinedInitials(), true),
            new FirstnameMapper(),
            new MiddlenameMapper(true),
        ]);

        return $parser;
    }

    protected function getThirdSegmentParser(): Parser
    {
        $parser = new Parser();

        $parser->setMappers([
            new SuffixMapper($this->getSuffixes(), true, 0),
        ]);

        return $parser;
    }

    /**
     * get the mappers for this parser
     *
     * @return array<int, \CodeByZach\NameParser\Mapper\AbstractMapper>
     */
    public function getMappers(): array
    {
        if (empty($this->mappers)) {
            $this->setMappers([
                new NicknameMapper($this->getNicknameDelimiters()),
                new SalutationMapper($this->getSalutations(), $this->getMaxSalutationIndex()),
                new SuffixMapper($this->getSuffixes()),
                new InitialMapper($this->getMaxCombinedInitials()),
                new LastnameMapper($this->getPrefixes()),
                new FirstnameMapper(),
                new MiddlenameMapper(),
            ]);
        }

        return $this->mappers;
    }

    /**
     * set the mappers for this parser
     *
     * @param  array<int, \CodeByZach\NameParser\Mapper\AbstractMapper>  $mappers
     */
    public function setMappers(array $mappers): Parser
    {
        $this->mappers = $mappers;

        return $this;
    }

    /**
     * normalize the name
     */
    protected function normalize(string $name): string
    {
        $whitespace = $this->getWhitespace();

        $name = trim($name);

        return preg_replace('/[' . preg_quote($whitespace, '/') . ']+/', ' ', $name) ?? $name;
    }

    /**
     * get a string of characters that are supposed to be treated as whitespace
     */
    public function getWhitespace(): string
    {
        return $this->whitespace;
    }

    /**
     * set the string of characters that are supposed to be treated as whitespace
     */
    public function setWhitespace(string $whitespace): Parser
    {
        $this->whitespace = $whitespace;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    protected function getPrefixes(): array
    {
        $prefixes = [];

        foreach ($this->languages as $language) {
            $prefixes += $language->getLastnamePrefixes();
        }

        return $prefixes;
    }

    /**
     * @return array<string, string>
     */
    protected function getSuffixes(): array
    {
        $suffixes = [];

        foreach ($this->languages as $language) {
            $suffixes += $language->getSuffixes();
        }

        return $suffixes;
    }

    /**
     * @return array<string, string>
     */
    protected function getSalutations(): array
    {
        $salutations = [];

        foreach ($this->languages as $language) {
            $salutations += $language->getSalutations();
        }

        return $salutations;
    }

    /**
     * @return array<string, string>
     */
    public function getNicknameDelimiters(): array
    {
        return $this->nicknameDelimiters;
    }

    /**
     * @param  array<string, string>  $nicknameDelimiters
     */
    public function setNicknameDelimiters(array $nicknameDelimiters): Parser
    {
        $this->nicknameDelimiters = $nicknameDelimiters;

        return $this;
    }

    public function getMaxSalutationIndex(): int
    {
        return $this->maxSalutationIndex;
    }

    public function setMaxSalutationIndex(int $maxSalutationIndex): Parser
    {
        $this->maxSalutationIndex = $maxSalutationIndex;

        return $this;
    }

    public function getMaxCombinedInitials(): int
    {
        return $this->maxCombinedInitials;
    }

    public function setMaxCombinedInitials(int $maxCombinedInitials): Parser
    {
        $this->maxCombinedInitials = $maxCombinedInitials;

        return $this;
    }
}
