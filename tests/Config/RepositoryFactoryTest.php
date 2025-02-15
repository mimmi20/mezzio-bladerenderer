<?php

/**
 * This file is part of the mimmi20/mezzio-bladerenderer package.
 *
 * Copyright (c) 2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\Mezzio\BladeRenderer\Config;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use InvalidArgumentException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionException;

use function assert;

final class RepositoryFactoryTest extends TestCase
{
    private RepositoryFactory $factory;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->factory = new RepositoryFactory();
    }

    /** @throws void */
    #[Override]
    protected function tearDown(): void
    {
        Container::setInstance(null);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache1(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::never())
            ->method('get');
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache2(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache3(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache4(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['templates' => false]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache5(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['templates' => []]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache6(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['templates' => ['cache-path' => true]]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutCache7(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['templates' => ['cache-path' => '']]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        assert($container instanceof ContainerInterface);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('a cache path is required');

        ($this->factory)($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testInvocation(): void
    {
        $cachePath = 'tests/cache';

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['templates' => ['cache-path' => $cachePath, 'paths' => true]]);
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $result = ($this->factory)($container);

        self::assertInstanceOf(Repository::class, $result);
    }
}
