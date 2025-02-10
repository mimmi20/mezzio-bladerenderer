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

use ArrayObject;
use DateTime;
use DateTimeInterface;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\View;
use Jenssegers\Blade\Blade;
use LogicException;
use Mimmi20\Mezzio\BladeRenderer\Components\MediaImage;
use Override;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

use function assert;
use function file_get_contents;
use function is_string;
use function trim;

final class BladeRendererTest extends TestCase
{
    private BladeRenderer $bladeRenderer;

    /**
     * @throws TypeError
     * @throws LogicException
     */
    #[Override]
    protected function setUp(): void
    {
        $app = Container::getInstance();
        $app->bind(ApplicationContract::class, Container::class);
        $app->alias('view', FactoryContract::class);

        $blade = new Blade(['tests/views'], 'tests/cache', $app);

        $blade->directive(
            'datetime',
            static fn (string $expression): string => '<?php echo with(' . $expression . ')->format(\'F d, Y g:i a\'); ?>',
        );

        $blade->if('ifdate', static fn (mixed $date): bool => $date instanceof DateTimeInterface);

        $blade->compiler()->components([
            'media-image' => MediaImage::class,
            'components.anonymous.alert' => 'alert',
        ]);

        $this->bladeRenderer = new BladeRenderer($blade);
    }

    /** @throws Exception */
    public function testCompilerGetter(): void
    {
        self::assertInstanceOf(BladeCompiler::class, $this->bladeRenderer->compiler());
    }

    /** @throws Exception */
    public function testBasic(): void
    {
        $output = $this->bladeRenderer->render('basic');
        self::assertSame('hello world', trim($output));
    }

    /** @throws Exception */
    public function testExists(): void
    {
        self::assertFalse($this->bladeRenderer->exists('nonexistentview'));
    }

    /** @throws Exception */
    public function testVariables(): void
    {
        $output = $this->bladeRenderer->render('variables', ['name' => 'John Doe']);
        self::assertSame('hello John Doe', trim($output));
    }

    /** @throws Exception */
    public function testNonBlade(): void
    {
        $output = $this->bladeRenderer->render('plain');
        self::assertSame('{{ this is plain php }}', trim($output));
    }

    /** @throws Exception */
    public function testShare(): void
    {
        $this->bladeRenderer->share('name', 'John Doe');

        $output = $this->bladeRenderer->render('variables');
        self::assertSame('hello John Doe', trim($output));
    }

    /** @throws Exception */
    public function testComposer(): void
    {
        $this->bladeRenderer->composer('variables', static function (View $view): void {
            $offset = $view->offsetGet('name');
            assert(is_string($offset));

            $view->with('name', 'John Doe and ' . $offset);
        });

        $output = $this->bladeRenderer->render('variables', ['name' => 'Jane Doe']);
        self::assertSame('hello John Doe and Jane Doe', trim($output));
    }

    /** @throws Exception */
    public function testComposer2(): void
    {
        $this->bladeRenderer->composer('variables', static function (View $view): void {
            $offset = $view->offsetGet('name');
            assert(is_string($offset));

            $view->with('name', 'John Doe and ' . $offset);
        });

        $output = $this->bladeRenderer->render('variables', new ArrayObject(['name' => 'Jane Doe']));
        self::assertSame('hello John Doe and Jane Doe', trim($output));
    }

    /** @throws Exception */
    public function testCreator(): void
    {
        $this->bladeRenderer->creator('variables', static function (View $view): void {
            $view->with('name', 'John Doe');
        });
        $this->bladeRenderer->composer('variables', static function (View $view): void {
            $offset = $view->offsetGet('name');
            assert(is_string($offset));

            $view->with('name', 'Jane Doe and ' . $offset);
        });

        $output = $this->bladeRenderer->render('variables');
        self::assertSame('hello Jane Doe and John Doe', trim($output));
    }

    /** @throws Exception */
    public function testRenderAlias(): void
    {
        $output = $this->bladeRenderer->render('basic');
        self::assertSame('hello world', trim($output));
    }

    /** @throws Exception */
    public function testDirective(): void
    {
        $output = $this->bladeRenderer->render('directive', ['birthday' => new DateTime('1989/08/19')]);
        self::assertSame('Your birthday is August 19, 1989 12:00 am', trim($output));
    }

    /** @throws Exception */
    public function testIf(): void
    {
        $output = $this->bladeRenderer->render('if', ['birthday' => new DateTime('1989/08/19')]);
        self::assertSame('Birthday August 19, 1989 12:00 am detected', trim($output));
    }

    /** @throws Exception */
    public function testOther(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@doe.com',
            ],
            [
                'id' => 2,
                'name' => 'Jen Doe',
                'email' => 'jen.doe@example.com',
            ],
            [
                'id' => 3,
                'name' => 'Jerry Doe',
                'email' => 'jerry.doe@doe.com',
            ],
        ];

        $output = $this->bladeRenderer->render('other', [
            'users' => $users,
            'name' => '<strong>John</strong>',
            'authenticated' => false,
        ]);

        self::assertSame($output, $this->expected('other'));
    }

    /** @throws Exception */
    public function testExtends(): void
    {
        $output = $this->bladeRenderer->render('extends');

        self::assertSame($output, $this->expected('extends'));
    }

    /** @throws Exception */
    public function testRenderTest(): void
    {
        $output = $this->bladeRenderer->render('test');
        self::assertSame(
            '<h1>There has been an error loading image "Background"</h1>',
            trim($output),
        );
    }

    /** @throws void */
    private function expected(string $file): string
    {
        $filePath = __DIR__ . '/../expected/' . $file . '.html';

        return (string) file_get_contents($filePath);
    }
}
