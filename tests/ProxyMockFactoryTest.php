<?php

namespace timesplinter\Tests\ProxyMock;

use PHPUnit\Framework\TestCase;
use timesplinter\ProxyMock\ProxyMockFactory;
use timesplinter\Tests\ProxyMock\Resources\Service;
use timesplinter\Tests\ProxyMock\Resources\ServiceInterface;

class ProxyMockFactoryTest extends TestCase
{
    public function testWithInterface()
    {
        $factory = new ProxyMockFactory();

        $mock = $this->getMockBuilder(ServiceInterface::class)->setMethods(['run'])->getMockForAbstractClass();
        $mock->expects(self::once())->method('run')->willReturn(3);

        $proxyMock = $factory->create(ServiceInterface::class);
        $proxyMock->setMock($mock);

        self::assertSame($mock, $proxyMock->getMock());

        self::assertSame(3, $proxyMock->run());
    }

    public function testWithClass()
    {
        $factory = new ProxyMockFactory();

        $mock = $this->getMockBuilder(Service::class)->setMethods(['run'])->getMock();
        $mock->expects(self::once())->method('run')->willReturn(3);

        $proxyMock = $factory->create(Service::class);
        $proxyMock->setMock($mock);

        self::assertSame($mock, $proxyMock->getMock());

        self::assertSame(3, $proxyMock->run());
    }

    public function testWithClassImplementsInterface()
    {
        $factory = new ProxyMockFactory();

        $mock = $this->getMockBuilder(Service::class)->setMethods(['run'])->getMock();
        $mock->expects(self::once())->method('run')->willReturn(3);

        $proxyMock = $factory->create(ServiceInterface::class);
        $proxyMock->setMock($mock);

        self::assertSame($mock, $proxyMock->getMock());

        self::assertSame(3, $proxyMock->run());
    }

    public function testWithMultipleSetMock()
    {
        $factory = new ProxyMockFactory();

        $mock1 = $this->getMockBuilder(Service::class)->setMethods(['run'])->getMock();
        $mock1->expects(self::once())->method('run')->willReturn(3);

        $mock2 = $this->getMockBuilder(Service::class)->setMethods(['run'])->getMock();
        $mock2->expects(self::once())->method('run')->willReturn(4);

        $proxyMock = $factory->create(ServiceInterface::class);
        $proxyMock->setMock($mock1);

        self::assertSame($mock1, $proxyMock->getMock());

        self::assertSame(3, $proxyMock->run());

        $proxyMock->setMock($mock2);

        self::assertSame($mock2, $proxyMock->getMock());

        self::assertSame(4, $proxyMock->run());
    }
}
