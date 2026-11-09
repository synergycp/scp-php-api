<?php

use Scp\Api\Api;
use Scp\Api\ApiError;
use Scp\Api\ApiResponse;
use Scp\Api\ApiTransporter;

class ApiTest extends \Scp\TestCase
{

    public function testPostBodyKeepsUnicodeIntact()
    {
        $transport = Mockery::mock(ApiTransporter::class);
        $response = Mockery::mock(ApiResponse::class);
        $api = new Api('https://scp.example/api', 'key');
        $api->setTransport($transport);

        $transport->shouldReceive('call')
            ->once()
            ->with(
                'POST',
                'https://scp.example/api/client?key=key',
                Mockery::on(function ($body) {
                    $decoded = json_decode($body, true);

                    return strpos($body, '张') !== false
                        && $decoded['first'] === '张'
                        && $decoded['last'] === '伟';
                }),
                Mockery::type('array')
            )
            ->andReturn($response);

        $this->assertSame($response, $api->post('client', ['first' => '张', 'last' => '伟']));
    }

    public function testPostRejectsBodyThatCannotBeEncoded()
    {
        $transport = Mockery::mock(ApiTransporter::class);
        $transport->shouldReceive('call')->never();
        $api = new Api('https://scp.example/api', 'key');
        $api->setTransport($transport);

        $this->expectException(ApiError::class);
        $api->post('client', ['first' => "\xB1\x31"]);
    }
}
