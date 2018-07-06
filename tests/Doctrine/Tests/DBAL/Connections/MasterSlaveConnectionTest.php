<?php

namespace Doctrine\Tests\DBAL\Connections;

use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Doctrine\Tests\DbalTestCase;
use Exception;

class MasterSlaveConnectionTest extends DbalTestCase
{
    public function testPing()
    {
        $driverMock           = $this->createMock(Driver::class);
        $driverConnectionMock = $this->createMock(Connection::class);
        $driverConnectionMock
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue($this->createMock(Statement::class)))
        ;

        $driverMock->expects($this->any())
            ->method('connect')
            ->will($this->returnValue($driverConnectionMock));

        $connection = new MasterSlaveConnection([
            'platform' => new MockPlatform(),
            'driver' => 'pdo_mysql',
            'master' => [
                'user' => '',
                'password' => '',
                'host' => '',
                'dbname' => '',
            ],
            'slaves' => [
                [
                    'user' => 'slave1',
                    'password' => '',
                    'host' => '',
                    'dbname' => '',
                ],
                [
                    'user' => 'slave2',
                    'password' => '',
                    'host' => '',
                    'dbname' => '',
                ],
            ],
        ], $driverMock);

        $this->assertTrue($connection->ping());

        $driverConnectionMock
            ->expects($this->any())
            ->method('query')
            ->willThrowException(new Exception('Query error.'))
        ;

        $this->assertFalse($connection->ping());
    }
}