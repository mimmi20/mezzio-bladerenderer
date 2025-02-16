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

use Illuminate\Contracts\View\View;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function trim;

final class BladeRendererTest extends TestCase
{
    /** @throws Exception */
    public function testCompilerGetter(): void
    {
        $factory  = $this->createMock(Factory::class);
        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        self::assertSame($compiler, $bladeRenderer->compiler());
    }

    /** @throws Exception */
    public function testBasic(): void
    {
        $name     = 'basic';
        $expected = 'hello world';

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('render')
            ->willReturn($expected);

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::once())
            ->method('make')
            ->with($name, [], [])
            ->willReturn($view);
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::never())
            ->method('composer');
        $factory->expects(self::never())
            ->method('share');
        $factory->expects(self::never())
            ->method('exists');
        $factory->expects(self::never())
            ->method('creator');

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        $output = $bladeRenderer->render($name);
        self::assertSame($expected, trim($output));
    }

    /** @throws Exception */
    public function testExists(): void
    {
        $name = 'nonexistentview';

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::never())
            ->method('make');
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::never())
            ->method('composer');
        $factory->expects(self::never())
            ->method('share');
        $factory->expects(self::once())
            ->method('exists')
            ->with($name)
            ->willReturn(false);
        $factory->expects(self::never())
            ->method('creator');

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        self::assertFalse($bladeRenderer->exists($name));
    }

    /** @throws Exception */
    public function testVariables(): void
    {
        $name       = 'variables';
        $expected   = 'hello John Doe';
        $parameters = ['name' => 'John Doe'];

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('render')
            ->willReturn($expected);

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::once())
            ->method('make')
            ->with($name, $parameters, [])
            ->willReturn($view);
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::never())
            ->method('composer');
        $factory->expects(self::never())
            ->method('share');
        $factory->expects(self::never())
            ->method('exists');
        $factory->expects(self::never())
            ->method('creator');

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        $output = $bladeRenderer->render($name, $parameters);
        self::assertSame($expected, trim($output));
    }

    /** @throws Exception */
    public function testShare(): void
    {
        $name     = 'John Doe';
        $expected = 'hello John Doe';

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::never())
            ->method('make');
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::never())
            ->method('composer');
        $factory->expects(self::once())
            ->method('share')
            ->with('name', $name)
            ->willReturn($expected);
        $factory->expects(self::never())
            ->method('exists');
        $factory->expects(self::never())
            ->method('creator');

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        self::assertSame($expected, $bladeRenderer->share('name', $name));
    }

    /** @throws Exception */
    public function testComposer(): void
    {
        $name     = 'John Doe';
        $expected = ['abc' => 'adrf'];

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::never())
            ->method('make');
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::once())
            ->method('composer')
            ->with($name)
            ->willReturn($expected);
        $factory->expects(self::never())
            ->method('share');
        $factory->expects(self::never())
            ->method('exists');
        $factory->expects(self::never())
            ->method('creator');

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        self::assertSame(
            $expected,
            $bladeRenderer->composer($name, static function (): void {
            }),
        );
    }

    /** @throws Exception */
    public function testCreator(): void
    {
        $name     = 'John Doe';
        $expected = ['abc' => 'adrf'];

        $factory = $this->createMock(Factory::class);
        $factory->expects(self::never())
            ->method('make');
        $factory->expects(self::never())
            ->method('addLocation');
        $factory->expects(self::never())
            ->method('getFinder');
        $factory->expects(self::never())
            ->method('composer');
        $factory->expects(self::never())
            ->method('share');
        $factory->expects(self::never())
            ->method('exists');
        $factory->expects(self::once())
            ->method('creator')
            ->with($name)
            ->willReturn($expected);

        $compiler = $this->createMock(BladeCompiler::class);
        $finder   = $this->createMock(FileViewFinder::class);

        $bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);

        self::assertSame($expected, $bladeRenderer->creator($name, static function (View $view): void {
            $view->with('name', 'John Doe');
        }));
    }
}
