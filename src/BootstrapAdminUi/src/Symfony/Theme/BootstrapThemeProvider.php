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

namespace Sylius\BootstrapAdminUi\Symfony\Theme;

use Sylius\AdminUi\Symfony\Theme\Attribute\AsTheme;
use Sylius\AdminUi\Symfony\Theme\ThemeProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

#[AsTheme(name: 'bootstrap', priority: 0)]
class BootstrapThemeProvider implements ThemeProviderInterface
{
    public function loadConfiguration(ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(\dirname(__DIR__, 3) . '/config'),
        );

        $loader->load('app.php');
        $loader->load('services.php');
    }
}
