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
use LogicException;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

use function trim;

final class BladeRendererTest extends TestCase
{
    /**
     * @throws TypeError
     * @throws LogicException
     */
    #[Override]
    protected function setUp(): void
    {
//        $fa  = $this->createMock(Factory::class);
//        $c = $this->createMock(BladeCompiler::class);
//        $fi   = $this->createMock(FileViewFinder::class);
//
//        $rendererContainer = new \Mimmi20\Mezzio\BladeRenderer\Container\Container();
//        $rendererContainer->singleton('view', function() use ($fa) {
//            return $fa;
//        });
//
//        $rendererContainer->singleton('blade.compiler', function () use ($c) {
//            return $c;
//        });
//
//        $rendererContainer->bind('view.finder', function () use ($fi) {
//            return $fi;
//        });
//
// //        $blade = new Blade(['tests/views'], 'tests/cache', $app);
// //
// //        $blade->if('ifdate', static fn (mixed $date): bool => $date instanceof DateTimeInterface);
// //
// //        $blade->compiler()->components([
// //            'media-image' => MediaImage::class,
// //            'components.anonymous.alert' => 'alert',
// //        ]);
//
//        $factory  = $rendererContainer->get('view');
//        $compiler = $rendererContainer->get('blade.compiler');
//        $finder   = $rendererContainer->get('view.finder');
//
//        $this->bladeRenderer = new BladeRenderer(factory: $factory, compiler: $compiler, finder: $finder);
    }

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
//        $blade->directive(
//            'datetime',
        /*            static fn (string $expression): string => '<?php echo with(' . $expression . ')->format(\'F d, Y g:i a\'); ?>', */
//        );
//
//        $blade->if('ifdate', static fn (mixed $date): bool => $date instanceof DateTimeInterface);
//
//        $blade->compiler()->components([
//            'media-image' => MediaImage::class,
//            'components.anonymous.alert' => 'alert',
//        ]);

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

//    /** @throws Exception */
//    public function testComposer(): void
//    {
//        self::markTestSkipped();
//        $this->bladeRenderer->composer('variables', static function (View $view): void {
//            $offset = $view->offsetGet('name');
//            assert(is_string($offset));
//
//            $view->with('name', 'John Doe and ' . $offset);
//        });
//
//        $output = $this->bladeRenderer->render('variables', ['name' => 'Jane Doe']);
//        self::assertSame('hello John Doe and Jane Doe', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testComposer2(): void
//    {
//        self::markTestSkipped();
//        $this->bladeRenderer->composer('variables', static function (View $view): void {
//            $offset = $view->offsetGet('name');
//            assert(is_string($offset));
//
//            $view->with('name', 'John Doe and ' . $offset);
//        });
//
//        $output = $this->bladeRenderer->render('variables', new ArrayObject(['name' => 'Jane Doe']));
//        self::assertSame('hello John Doe and Jane Doe', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testCreator(): void
//    {
//        self::markTestSkipped();
//        $this->bladeRenderer->creator('variables', static function (View $view): void {
//            $view->with('name', 'John Doe');
//        });
//        $this->bladeRenderer->composer('variables', static function (View $view): void {
//            $offset = $view->offsetGet('name');
//            assert(is_string($offset));
//
//            $view->with('name', 'Jane Doe and ' . $offset);
//        });
//
//        $output = $this->bladeRenderer->render('variables');
//        self::assertSame('hello Jane Doe and John Doe', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testRenderAlias(): void
//    {
//        self::markTestSkipped();
//        $output = $this->bladeRenderer->render('basic');
//        self::assertSame('hello world', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testDirective(): void
//    {
//        self::markTestSkipped();
//        $output = $this->bladeRenderer->render('directive', ['birthday' => new DateTime('1989/08/19')]);
//        self::assertSame('Your birthday is August 19, 1989 12:00 am', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testIf(): void
//    {
//        self::markTestSkipped();
//        $output = $this->bladeRenderer->render('if', ['birthday' => new DateTime('1989/08/19')]);
//        self::assertSame('Birthday August 19, 1989 12:00 am detected', trim($output));
//    }
//
//    /** @throws Exception */
//    public function testOther(): void
//    {
//        self::markTestSkipped();
//        $users = [
//            [
//                'id' => 1,
//                'name' => 'John Doe',
//                'email' => 'john.doe@doe.com',
//            ],
//            [
//                'id' => 2,
//                'name' => 'Jen Doe',
//                'email' => 'jen.doe@example.com',
//            ],
//            [
//                'id' => 3,
//                'name' => 'Jerry Doe',
//                'email' => 'jerry.doe@doe.com',
//            ],
//        ];
//
//        $output = $this->bladeRenderer->render('other', [
//            'users' => $users,
//            'name' => '<strong>John</strong>',
//            'authenticated' => false,
//        ]);
//
//        self::assertSame($output, $this->expected('other'));
//    }
//
//    /** @throws Exception */
//    public function testExtends(): void
//    {
//        self::markTestSkipped();
//        $output = $this->bladeRenderer->render('extends');
//
//        self::assertSame($output, $this->expected('extends'));
//    }
//
//    /** @throws Exception */
//    public function testRenderTest(): void
//    {
//        self::markTestSkipped();
//        $output = $this->bladeRenderer->render('test');
//        self::assertSame(
//            '<h1>There has been an error loading image "Background"</h1>',
//            trim($output),
//        );
//    }
//
//    /** @throws void */
//    private function expected(string $file): string
//    {
//        $filePath = __DIR__ . '/../expected/' . $file . '.html';
//
//        return (string) file_get_contents($filePath);
//    }
}
