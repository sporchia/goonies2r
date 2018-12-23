<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeTest extends TestCase
{
    /**
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testRootRedirectsToLocale()
    {
        $response = $this->get('/');

        $response->assertStatus(301);
        $response->assertRedirect('/en');
    }
}
