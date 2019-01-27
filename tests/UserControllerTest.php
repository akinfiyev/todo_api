<?php

namespace App\Tests;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistration()
    {
        $data = json_encode([
            'email' => 'email' . time() . "@gmail.com",
            'plainPassword' => "12345"
        ]);

        $client = new Client();
        $response = $client->post('http://127.0.0.10:8080/api/users', [
            'body' => $data
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('apiToken', $responseBody);
    }
}
