<?php

namespace CodeByZach\NameParser;

interface LanguageInterface
{
    public function getSuffixes(): array;

    public function getLastnamePrefixes(): array;

    public function getSalutations(): array;
}
