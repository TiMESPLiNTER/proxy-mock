<?php

namespace timesplinter\Tests\ProxyMock\Resources;

class Service implements ServiceInterface
{
    public function run()
    {
        return 5;
    }
}
