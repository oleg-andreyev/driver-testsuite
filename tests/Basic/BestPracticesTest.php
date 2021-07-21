<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Driver\CoreDriver;
use Behat\Mink\Tests\Driver\TestCase;
use Behat\Mink\Driver\CoreDriver;

/**
 * This testcase ensures that the driver implementation follows recommended practices for drivers.
 */
final class BestPracticesTest extends TestCase
{
    public function testExtendsCoreDriver(): void
    {
        $driver = $this->createDriver();

        $this->assertInstanceOf(CoreDriver::class, $driver);
    }

    /**
     * @depends testExtendsCoreDriver
     *
     * @return void
     */
    public function testImplementFindXpath(): void
    {
        $driver = $this->createDriver();

        $this->assertNotImplementMethod('find', $driver, 'The driver should overwrite `findElementXpaths` rather than `find` for forward compatibility with Mink 2.');
        $this->assertImplementMethod('findElementXpaths', $driver, 'The driver must be able to find elements.');
        $this->assertNotImplementMethod('setSession', $driver, 'The driver should not deal with the Session directly for forward compatibility with Mink 2.');
    }

    /**
     * @dataProvider provideRequiredMethods
     *
     * @return void
     */
    public function testImplementBasicApi(string $method): void
    {
        $driver = $this->createDriver();

        $this->assertImplementMethod($method, $driver, 'The driver is unusable when this method is not implemented.');
    }

    public function provideRequiredMethods(): array
    {
        return [
            ['start'],
            ['isStarted'],
            ['stop'],
            ['reset'],
            ['visit'],
            ['getCurrentUrl'],
            ['getContent'],
            ['click'],
        ];
    }

    private function assertImplementMethod(string $method, \Behat\Mink\Driver\DriverInterface $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' ' . $reason;
        }

        $this->assertNotSame(CoreDriver::class, $refMethod->getDeclaringClass()->name, $message);
    }

    private function assertNotImplementMethod(string $method, \Behat\Mink\Driver\DriverInterface $object, string $reason = ''): void
    {
        $ref = new \ReflectionClass(get_class($object));
        $refMethod = $ref->getMethod($method);

        $message = sprintf('The driver should not implement the `%s` method.', $method);

        if ('' !== $reason) {
            $message .= ' ' . $reason;
        }

        $this->assertSame(CoreDriver::class, $refMethod->getDeclaringClass()->name, $message);
    }
}
