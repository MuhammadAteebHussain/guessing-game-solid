<?php

namespace App\Contract;

interface IGuessingGame
{
    public function getMaskedWords(): array;
    public function guessWord(string $guess): string;
}