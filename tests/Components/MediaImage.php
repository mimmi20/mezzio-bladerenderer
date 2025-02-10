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

namespace Mimmi20\Mezzio\BladeRenderer\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Override;

/**
 * Very rough example with some wordpress stuff
 *
 * @phpcs:disable SlevomatCodingStandard.Classes.ForbiddenPublicProperty.ForbiddenPublicProperty
 */
final class MediaImage extends Component
{
    /** @api */
    public bool $image = false;

    /**
     * @param string $wordpresstitle title of WP image attachment to process
     * @param string $size           size parameter for wp image functions
     * @param string $class          all the classes to assign to the img tag
     * @param int    $preload        if the image needs to be preloaded
     *
     * @throws void
     */
    public function __construct(
        public readonly string $wordpresstitle,
        public readonly string $size = 'full',
        public readonly string $class = 'media-image',
        public readonly int $preload = 0,
    ) {
        // nothing to do
    }

    /** @throws void */
    #[Override]
    public function render(): View
    {
        return $this->view('components.media-image');
    }
}
