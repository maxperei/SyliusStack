<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\AdminUi\Fixtures\AcmeAdminUi;

use Sylius\AdminUi\Symfony\Theme\HasThemeProviderInterface;
use Sylius\AdminUi\Symfony\Theme\ThemeProviderInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Tests\Sylius\AdminUi\Fixtures\AcmeAdminUi\DependencyInjection\AcmeAdminUiThemeProvider;

class AcmeAdminUiBundle extends AbstractBundle implements HasThemeProviderInterface
{
    public function getThemeProvider(): ThemeProviderInterface
    {
        return new AcmeAdminUiThemeProvider();
    }
}
