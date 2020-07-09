<?php

namespace App\Tests\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    public function testTokenGeneration()
    {
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->getRandomSecureToken(30);

        $this->assertEquals(30, strlen($token));

        //$this->assertTrue(ctype_alnum($token), 'Token contains invalid characters');
        $this->assertEquals(1, preg_match("/^[A-Za-z0-9]+$/", $token),'Token contains non valid characters');
    }
}