<?php

namespace timesplinter\ProxyMock;

use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProxyMockInterface
 * @package timesplinter\ServiceMock
 */
interface ProxyMockInterface
{

    /**
     * @return MockObject
     */
    public function getMock(): MockObject;

    /**
     * @param MockObject $mock
     */
    public function setMock(MockObject $mock);
}
