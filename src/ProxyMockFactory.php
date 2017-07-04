<?php

namespace timesplinter\ProxyMock;

/**
 * Class ServiceMockFactory
 * @package timesplinter\ServiceMock
 */
final class ProxyMockFactory implements ProxyMockFactoryInterface
{

    /**
     * @var array|string[]
     */
    private $registry = [];

    /**
     * @param string $className
     * @return ProxyMockInterface
     */
    public function create(string $className): ProxyMockInterface
    {

        if (false === isset($this->registry[$className])) {
            $reflector = new \ReflectionClass($className);

            $proxyClassName = 'ProxyMock_' . uniqid() . '_' . $reflector->getShortName();
            $classCode = $this->renderClass($reflector, $proxyClassName);

            $this->evalClass($classCode, $proxyClassName);

            $this->registry[$className] = $proxyClassName;
        }

        return new $this->registry[$className]();
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    private function renderMethod(\ReflectionMethod $method)
    {
        $methodBody = file_get_contents(__DIR__.'/Generator/method.tpl');

        $modifier = '';

        if ($method->isFinal()) {
            throw new \RuntimeException(
                sprintf(
                    'Can not proxy final method %s of class %s',
                    $method->getName(),
                    $method->getDeclaringClass()->getName()
                )
            );
        }

        if ($method->isPublic()) {
            $modifier .= 'public ';
        } elseif ($method->isProtected()) {
            $modifier .= 'protected ';
        } elseif ($method->isPrivate()) {
            $modifier .= 'private ';
        }

        if ($method->isStatic()) {
            $modifier .= 'static ';
        }

        $argumentsCall = $this->getMethodParameters($method, true);

        return strtr($methodBody, [
            '<modifier>' => trim($modifier),
            '<methodName>' => $method->getName(),
            '<arguments>' => $this->getMethodParameters($method),
            '<argumentsCall>' => '' !== $argumentsCall ? $argumentsCall : null,
            '<returnType>' => $method->hasReturnType() ? ': '.$method->getReturnType() : null
        ]);
    }

    /**
     * Returns the parameters of a function or method.
     *
     * @param \ReflectionMethod $method
     * @param bool             $forCall
     *
     * @return string
     */
    private function getMethodParameters(\ReflectionMethod $method, $forCall = false)
    {
        $parameters = [];
        foreach ($method->getParameters() as $i => $parameter) {
            $name = '$' . $parameter->getName();
            /* Note: PHP extensions may use empty names for reference arguments
             * or "..." for methods taking a variable number of arguments.
             */
            if ($name === '$' || $name === '$...') {
                $name = '$arg' . $i;
            }
            if ($parameter->isVariadic()) {
                if ($forCall) {
                    continue;
                }
                $name = '...' . $name;
            }
            $nullable        = '';
            $default         = '';
            $reference       = '';
            $typeDeclaration = '';
            if (!$forCall) {
                if ($parameter->hasType() && (string) $parameter->getType() !== 'self') {
                    if (version_compare(PHP_VERSION, '7.1', '>=')
                        && $parameter->allowsNull()
                        && !$parameter->isVariadic()
                    ) {
                        $nullable = '?';
                    }
                    $typeDeclaration = (string) $parameter->getType() . ' ';
                } elseif ($parameter->isArray()) {
                    $typeDeclaration = 'array ';
                } elseif ($parameter->isCallable()) {
                    $typeDeclaration = 'callable ';
                } else {
                    try {
                        $class = $parameter->getClass();
                    } catch (\ReflectionException $e) {
                        throw new \RuntimeException(
                            sprintf(
                                'Cannot mock %s::%s() because a class or ' .
                                'interface used in the signature is not loaded',
                                $method->getDeclaringClass()->getName(),
                                $method->getName()
                            ),
                            0,
                            $e
                        );
                    }
                    if ($class !== null) {
                        $typeDeclaration = $class->getName() . ' ';
                    }
                }
                if (!$parameter->isVariadic()) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $value   = $parameter->getDefaultValue();
                        $default = ' = ' . var_export($value, true);
                    } elseif ($parameter->isOptional()) {
                        $default = ' = null';
                    }
                }
            }
            if ($parameter->isPassedByReference()) {
                $reference = '&';
            }
            $parameters[] = $nullable . $typeDeclaration . $reference . $name . $default;
        }
        return implode(', ', $parameters);
    }

    /**
     * @param \ReflectionClass $class
     * @param string $proxyClassName
     * @return string
     */
    private function renderClass(\ReflectionClass $class, string $proxyClassName)
    {

        $classDeclaration = $proxyClassName;

        if ($class->isFinal()) {
            throw new \RuntimeException(
                sprintf('Class %s is final and can therefor not be proxied', $class->getName())
            );
        } elseif ($class->isInterface()) {
            $classDeclaration .= ' implements ' . $class->getName() .', '.ProxyMockInterface::class;
        } else {
            $classDeclaration .= ' extends ' . $class->getName().' implements '.ProxyMockInterface::class;
        }

        $methodCode = '';

        foreach ($class->getMethods() as $method)
        {
            if (true === $method->isConstructor()) {
                continue;
            }

            $methodCode .= $this->renderMethod($method) . PHP_EOL;
        }

        $classBody = file_get_contents(__DIR__.'/Generator/class.tpl');

        return strtr($classBody, [
            '<classDeclaration>' => $classDeclaration,
            '<originalClassName>' => $class->getName(),
            '<methods>' => trim($methodCode),
        ]);
    }

    /**
     * @param string $code
     * @param string $className
     */
    private function evalClass($code, $className)
    {
        if (!class_exists($className, false)) {
            eval($code);
        }
    }
}
