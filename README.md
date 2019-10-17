ProxyMock
=========

**THIS PACKAGE IS ABANDONNED**

To replace a service of the Symfony container with a mock make a public alias on the service and then you're able to set
a mock for it in the test case (if you have access to the container which is the case in ̀Symfony's `KernelTestCase`).

**config/services.yaml**
```
services:
    my.service:
        class: My\Service
```

**config/services_test.yaml**
```
services:
    my.service.test:
        alias: my.service
        public: true
```

**Your test case `extends KernelTestCase`**
```php
use My\Service;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ConsumeUserMessagesFromCoreTest extends KernelTestCase
{
    public function testSomething(): void
    {
        // Access the service over the public alias defined in "services_test.yaml"
        self::$container->set('my.service.test', $this->getMockBuilder(Service::class)->getMock());
    }
}
```

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TiMESPLiNTER/proxy-mock/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TiMESPLiNTER/proxy-mock/?branch=master)

This library helps to create a proxy instance of a class which then can hold a PHPUnit
mock of it. That way you can manipulate the mock which sits inside the proxy class but
never have to change the proxy class.

This can be useful for example in cases of read-only containers where you can't
override services at runtime. (The dependency injection container component of 
Symfony 4 will most likely behave like that.)

```php
class Foo { ... }

$factory = new ProxyMockFactory();
$proxyMock = $factory->create(Foo::class);

// In a PHPUnit test case
$mock = $this->getMockBuilder(Foo::class)
    ->disableOriginalConstructor()
    ->getMock();

$proxyMock->setMock($mock);

```
