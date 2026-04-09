<?php

namespace App\Tests\Unit;

use App\Adapters\Wireguard\FileToPeer;
use App\Adapters\Wireguard\FileToServer;
use App\Domain\Wireguard\Server;
use App\Services\WireguardService;
use App\Services\WireguardWrapperInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Psr\Container\ContainerInterface;

class WireguardServiceTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function test_can_get_server(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $serverMock = new Server(
            '10.8.0.1',
            '127.0.0.1',
            34540,
            '8.8.8.8'
        );
        $serverMock->setKeys('C57OCFgebG4EtrWw', 'z3yCGsiZIu5IDzWX', 'jDreKQlSPG3MBSHG');

        $mock = $this->createMock(WireguardWrapperInterface::class);
        $mock->expects($this->once())
            ->method('getServer')
            ->willReturn($serverMock);

        $service = new WireguardService(
            new FileToServer($container),
            $mock,
        );

        $server = $service->server();

        $this->assertEquals('127.0.0.1', $server->getIp());
        $this->assertEquals('8.8.8.8', $server->getDns());
        $this->assertEquals(34540, $server->getListenPort());
        $this->assertEquals('10.8.0.1', $server->getAddress());
        $this->assertEquals('C57OCFgebG4EtrWw', $server->getPublicKey());
        $this->assertEquals('z3yCGsiZIu5IDzWX', $server->getPrivateKey());
        $this->assertEquals('jDreKQlSPG3MBSHG', $server->getPresharedKey());
    }
}
