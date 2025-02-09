<?php

namespace Mimmi20\Mezzio\BladeRenderer\Engine;

use Illuminate\Contracts\View\Engine;
use Illuminate\Filesystem\Filesystem;
use Laminas\View\Renderer\PhpRenderer;

class LaminasEngine implements Engine
{
    /**
     * The filesystem instance.
     *
     * @var PhpRenderer
     */
    private $renderer;

    /**
     * Create a new file engine instance.
     *
     * @param  PhpRenderer  $renderer
     * @return void
     */
    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array<mixed> $data
     * @return string
     */
    public function get($path, array $data = []): string
    {
        return $this->renderer->render($path, $data);
    }
}
