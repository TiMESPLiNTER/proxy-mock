<?php

namespace timesplinter\ProxyMock;

/**
 * Interface ServiceMockFactoryInterface
 * @package timesplinter\ServiceMock
 */
interface ProxyMockFactoryInterface
{

    /**
     * @param string $className
     * @return ProxyMockInterface
     */
    public function create(string $className): ProxyMockInterface;
}
