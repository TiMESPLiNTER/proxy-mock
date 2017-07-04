    <modifier> function <methodName>(<arguments>)<returnType>
    {
        if (null === $this->mock) {
            throw new \RuntimeException('There\'s no proxy mock set.');
        }

        return $this->mock-><methodName>(<argumentsCall>);
    }
