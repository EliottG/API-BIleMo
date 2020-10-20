<?php

namespace App\ControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MobileTest extends WebTestCase
{
    protected function createAuthenticatedClient($username = 'Client2', $password = 'password')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'username' => $username,
                'password' => $password,
            ))
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetMobiles()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/mobiles');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetOneMobile()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/mobile/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * There isn't any user with the ID 0
     */
    public function testGetNotFound()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/mobile/0');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}