<?php

namespace Scp\Client;

use PHPUnit\Framework\Attributes\DataProvider;

use Scp\Api\Api;
use Scp\Api\ApiResponse;

class ClientTest extends \Scp\TestCase {
	public function setUp(): void {
		$this->api = \Mockery::mock(Api::class);
		$this->response = \Mockery::mock(ApiResponse::class);
	}

/**
	 * @param array $info
	 *
	 */
	#[DataProvider('dataCreate')]
	public function testCreate(array $info, array $expectedInfo) {
		$this->response
			->shouldReceive('data')
			->andReturn((object) [
				'id' => $clientId = 1,
			]);
		$this->api
			->shouldReceive('post')
			->with('client/', $expectedInfo)
			->andReturn($this->response);
		$client = new Client($info, $this->api);
		$client->save();

		$this->assertEquals($client->id, $clientId);
	}

	public static function dataCreate() {
		return [
			[
				$data = [
					'test' => 'Test',
				], $data,
			],
		];
	}
}
