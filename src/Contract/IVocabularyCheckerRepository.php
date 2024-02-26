<?php

namespace App\Contract;

interface IVocabularyCheckerRepository
{
    public function isValid(string $word): bool;
}