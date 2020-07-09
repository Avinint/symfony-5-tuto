<?php

namespace App\Security;

//define('ALPHABET',  implode('', array_merge(range("A", "Z"), range("a", "z"), range(0, 9))));

class TokenGenerator
{
    //private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function getRandomSecureToken(int $length): string
    {
        $alphabet =  implode('', array_merge(range("A", "Z"), range("a", "z"), range(0, 9)));
        $maxNumber = strlen($alphabet);
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, $maxNumber - 1)];
        }

        return $token;
    }
}