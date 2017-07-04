class <classDeclaration>
{

    private $mock;

    public function __construct() { }

    public function getMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->mock;
    }

    public function setMock(\PHPUnit_Framework_MockObject_MockObject $mock)
    {
        if (false === $mock instanceof <originalClassName>) {
            throw new \InvalidArgumentException(
                sprintf('Mock must be an instance of %s', '<originalClassName>')
            );
        }

        $this->mock = $mock;
    }

    <methods>
}
