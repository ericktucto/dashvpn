<?php

namespace App\Tests\Unit;

use App\Services\WireguardService;
use App\Services\WireguardWrapperInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class WireguardServiceTest extends TestCase
{
    public function test_can_get_server(): void
    {
        $lines = explode("\n", file_get_contents('./tests/Unit/Fixtures/wg0.conf'));

        $config = new class {
            public function get(string $key) {
                if ($key === 'data') {
                    return [
                        'ip' => '127.0.0.1',
                        'dns' => '8.8.8.8',
                    ];
                }
            }
        };
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['config', $config]
            ]);

        $mock = $this->createMock(WireguardWrapperInterface::class);
        $mock->expects($this->once())
            ->method('getServer')
            ->willReturn($lines);
        $mock->method('getServerKeys')
            ->willReturn([
                'publicKey' => 'C57OCFgebG4EtrWw',
                'privateKey' => 'z3yCGsiZIu5IDzWX',
                'presharedKey' => 'jDreKQlSPG3MBSHG',
            ]);

        $service = new WireguardService($container, $mock);

        $server = $service->server();

        $this->assertEquals('127.0.0.1', $server->getIp());
        $this->assertEquals('8.8.8.8', $server->getDns());
        $this->assertEquals(34540, $server->getListenPort());
        $this->assertEquals('10.8.0.1/24', $server->getAddress());
        $this->assertEquals('C57OCFgebG4EtrWw', $server->getPublicKey());
        $this->assertEquals('z3yCGsiZIu5IDzWX', $server->getPrivateKey());
        $this->assertEquals('jDreKQlSPG3MBSHG', $server->getPresharedKey());
    }
}
