ProxyMock
=========
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