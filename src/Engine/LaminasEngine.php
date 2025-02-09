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

namespace Mimmi20\Mezzio\BladeRenderer\Engine;

use Illuminate\Contracts\View\Engine;
use Laminas\View\Exception\DomainException;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Renderer\PhpRenderer;
use Override;

final readonly class LaminasEngine implements Engine
{
    /**
     * Create a new file engine instance.
     *
     * @throws void
     */
    public function __construct(private PhpRenderer $renderer)
    {
        // nothing to do
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string       $path
     * @param  array<mixed> $data
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public function get($path, array $data = []): string
    {
        return $this->renderer->render($path, $data);
    }
}
