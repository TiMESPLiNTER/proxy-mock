class <classDeclaration>
{

    private $mock;

    public function __construct() { }

    public function getMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        if (null === $this->mock) {
            throw new \RuntimeException('There\'s no proxy mock set for class <originalClassName>.');
        }

        return $this->mock;
    }

    public function setMock(\PHPUnit_Framework_MockObject_MockObject $mock)
    {
        if (false === $mock instanceof <originalClassName>) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Mock must be an instance of %s and is %s',
                    '<originalClassName>',
                    get_class($mock)
                )
            );
        }

        $this->mock = $mock;
    }

    <methods>
}
