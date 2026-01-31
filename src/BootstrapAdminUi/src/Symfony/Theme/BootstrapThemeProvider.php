<?php

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
            new FileLocator(\dirname(__DIR__, 3) . '/config')
        );

        $loader->load('app.php');
    }
}
