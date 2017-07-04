<?php

namespace timesplinter\ProxyMock;

/**
 * Class ProxyMockInterface
 * @package timesplinter\ServiceMock
 */
interface ProxyMockInterface
{

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMock(): \PHPUnit_Framework_MockObject_MockObject;

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     */
    public function setMock(\PHPUnit_Framework_MockObject_MockObject $mock);
}
