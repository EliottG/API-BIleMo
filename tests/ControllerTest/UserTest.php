<?php

namespace App\ControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
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
    public function testGetUsers()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Put a user ID owned by the Client
     */
    public function testGetOneUser()
    {
        $id = 4;
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/user/' . $id);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testCreateUser()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('POST', '/api/user', [],[],[],
            json_encode([
                "username"=> "test",
                "email"=> "emailtest@mail.com"
            ])
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
    public function testCreateBadUser()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('POST', '/api/user', [],[],[],
            json_encode([
                "username"=> "",
                "email"=> "emailtest"
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testDeleteUser() 
    {
        $client = $this->createAuthenticatedClient();
        $client->request('DELETE', '/api/user/4');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
    /**
     * Put a user ID that doesn't belong to the Client
     */
    public function testUnauthorizedClient()
    {
        $id = 8;
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/user/' . $id);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
    /**
     * There isn't any user with the ID 0
     */
    public function testGetNotFoundUser()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/user/0');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
