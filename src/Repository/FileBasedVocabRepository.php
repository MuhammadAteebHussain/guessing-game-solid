<?php

namespace App\Repository;

use App\Contract\IVocabularyCheckerRepository;
use Exception;

class FileBasedVocabRepository implements IVocabularyCheckerRepository
{
    private array $validWords = [];
    private string $filePath = __DIR__ . '/../storage/wordlist.txt'; //this should be returned from .env

    private function loadValidWords(string $filePath): void
    {
        try {
            $handle = fopen($this->filePath, 'r');
            if (!$handle) {
                throw new Exception("Failed to open word list file.");
            }

            while (($line = fgets($handle)) !== false) {
                $this->validWords[] = trim($line);
            }

            fclose($handle);
        } catch (Exception $e) {
            throw new Exception("Error loading word list: " . $e->getMessage());
        }
    }

    public function isValid(string $word): bool
    {
        return in_array($word, $this->validWords, true);
    }
}
