<?php

namespace Scp\Api;

use Scp\Server\Server;

class ApiSingleSignOnTest extends \Scp\TestCase {
	public function testNoView() {
		$api = \Mockery::mock(Api::class);
		$apiKey = \Mockery::mock(ApiKey::class);

		$apiKey->shouldReceive('getAttribute')
			->with('key')
			->andReturn($key = 'asdf')
		;
		$api->shouldReceive('url')
			->with('auth/sso', [
				'key' => $key,
			])
			->andReturn($url = '/api/sso?key=' . $key)
		;
		$sso = new ApiSingleSignOn($apiKey, $api);

		$this->assertEquals($sso->url(), $url);
	}

	public function testViewServer() {
		$api = \Mockery::mock(Api::class);
		$apiKey = \Mockery::mock(ApiKey::class);
		$server = \Mockery::mock(Server::class);

		$apiKey->shouldReceive('getAttribute')
			->with('key')
			->andReturn($key = 'asdf')
		;
		$server->shouldReceive('getId')
			->andReturn($serverId = '1');
		$api->shouldReceive('url')
			->with('auth/sso', [
				'key' => $key,
				'view_type' => 'server',
				'view_id' => $serverId,
			])
			->andReturn($url = '/api/sso?key=' . $key)
		;

		$sso = new ApiSingleSignOn($apiKey, $api);
		$sso->view($server);

		$this->assertEquals($sso->url(), $url);
	}
}
