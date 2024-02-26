<?php

namespace App\Game;

use App\Contract\IGuessingGame;
use App\Contract\IVocabularyCheckerRepository;
use App\Exception\WordException;
use Exception;

class GuessingGame implements IGuessingGame
{
    private const EXACT_MATCH_MESSAGE = "Exact match! 10 points.";
    private const PARTIAL_MATCH_MESSAGE = "Partial Match.";
    private const INVALID_GUESS_MESSAGE = "Invalid guess. This is not a valid word or does not match the word length.";
    private const NO_MATCH_MESSAGE = "No Match!";
    private const DUPLICATE_ATTEMPT = "Multiple Attempt Same Word";
    private array $challengeWords;
    private array $maskedWords = [];

    /**
     * @throws Exception
     */
    public function __construct(array $words, readonly private IVocabularyCheckerRepository $vocabularyChecker)
    {
        $this->initializeGame($words);
    }

    private function initializeGame(array $words): void
    {
        $length = strlen($words[0]);
        $this->challengeWords = $words;
        foreach ($words as $i => $word){
            if($this->isWordLengthValid($word,$length) ){
                $this->maskedWords[] = $this->maskCharacters($word,$i,$length);
            }else{
                throw new WordException("All word lengths should be equal.");
            }
        }
    }

    public function getMaskedWords(): array
    {
        return $this->maskedWords;
    }

    public function guessWord(string $guess): string
    {
        if (!$this->vocabularyChecker->isValid($guess)) {
            return self::INVALID_GUESS_MESSAGE;
        }

        if ($this->isExactMatch($guess)) {
            return self::EXACT_MATCH_MESSAGE;
        }

        if ($score = $this->calculatePartialMatchScore($guess)) {
            return self::PARTIAL_MATCH_MESSAGE . " - " . $score;
        }

        return self::NO_MATCH_MESSAGE;
    }

    private function isWordLengthValid(string $word, int $expectedLength): bool
    {
        return strlen($word) === $expectedLength;
    }

    private function isExactMatch(string $guess): bool
    {
        foreach ($this->challengeWords as $index => $word) {
            if ($guess === $word && $this->maskedWords[$index]!==$guess) {
                $this->maskedWords[$index] = $word;
                return true;
            }
        }
        return false;
    }

    private function isResubmitted(string $guess): false
    {
        foreach ($this->challengeWords as $index => $word) {
            if ($guess === $word) {
                return false;
            }
        }
        return false;
    }

    private function calculatePartialMatchScore(string $guess): int
    {
        $score = 0;
        foreach ($this->challengeWords as $index => $word) {
            if ($guess == $word){
                return false;
            }
            for ($i = 0; $i < strlen($word); $i++) {
                if ($word[$i] === $guess[$i]) {
                    $this->maskedWords[$index][$i] = $word[$i];
                    $score++;
                }
            }
        }
        return $score;
    }

    function maskCharacters(string $word, int $count, int $length): string
    {
        $maskAllWord = str_repeat("*",$length);
        return  substr_replace($maskAllWord,$word[$count],$count,0);
    }
}
