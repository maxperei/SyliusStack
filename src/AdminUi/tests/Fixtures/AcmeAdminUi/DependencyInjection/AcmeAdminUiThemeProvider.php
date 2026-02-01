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

namespace Tests\Sylius\AdminUi\Fixtures\AcmeAdminUi\DependencyInjection;

use Sylius\AdminUi\Symfony\Theme\Attribute\AsTheme;
use Sylius\AdminUi\Symfony\Theme\ThemeProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[AsTheme(name: 'acme')]
class AcmeAdminUiThemeProvider implements ThemeProviderInterface
{
    public function loadConfiguration(ContainerBuilder $container): void
    {
        $container->setParameter('acme_admin_ui.loaded', true);
    }
}
