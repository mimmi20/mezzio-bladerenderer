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

namespace Mimmi20\Mezzio\BladeRenderer\Renderer;

use Illuminate\Container\Container;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionProperty;

final class BladeRendererFactoryTest extends TestCase
{
    private BladeRendererFactory $factory;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->factory = new BladeRendererFactory();
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
    public function testInvocation(): void
    {
        $factory  = $this->createMock(Factory::class);
        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $rendererContainer = new \Mimmi20\Mezzio\BladeRenderer\Container\Container();
        $rendererContainer->singleton('view', static fn () => $factory);

        $rendererContainer->singleton('blade.compiler', static fn () => $compiler);

        $rendererContainer->bind('view.finder', static fn () => $finder);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(\Mimmi20\Mezzio\BladeRenderer\Container\Container::class)
            ->willReturn($rendererContainer);
        $container->expects(self::never())
            ->method('has');

        $result = ($this->factory)($container);

        self::assertInstanceOf(BladeRenderer::class, $result);

        $f = new ReflectionProperty($result, 'factory');

        self::assertSame($factory, $f->getValue($result));

        $c = new ReflectionProperty($result, 'compiler');

        self::assertSame($compiler, $c->getValue($result));

        $n = new ReflectionProperty($result, 'finder');

        self::assertSame($finder, $n->getValue($result));
    }
}
