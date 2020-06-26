<?php

namespace Modules\CognitoGuard\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CognitoGuard\Tests\TestCase;

class CognitoJWTTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function autheduser_returns_jwt_claims()
    {
        $time = time();
        $res = $this->withHeaders($this->getAuthHeader($time))->json('GET', '/api/autheduser');
        $res->assertStatus(200);
        $res->assertJson([
            'username' => '9409a930-6094-42be-b719-abcdef123456',
        ]);
    }
}
