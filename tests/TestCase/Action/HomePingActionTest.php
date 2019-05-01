<?php

namespace App\Test\TestCase\Action;

use App\Test\TestCase\ApiTestCase;

/**
 * @coversDefaultClass \App\Action\HomePingAction
 */
class HomePingActionTest extends ApiTestCase
{
    /**
     * Test create object.
     *
     * @throws \Exception
     *
     * @return void
     *
     * @covers ::__invoke
     */
    public function testPing(): void
    {
        $request = $this->createServerRequest('POST', '/ping');
        $request = $this->withJson($request, ['username' => 'user', 'password' => 'user']);
        $response = $this->request($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertSame('{"username":"user","password":"user"}', $response->getBody()->__toString());
    }
}
