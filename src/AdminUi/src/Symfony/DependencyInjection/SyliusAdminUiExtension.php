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

namespace Sylius\AdminUi\Symfony\DependencyInjection;

use Sylius\AdminUi\Symfony\Theme\Attribute\AsTheme;
use Sylius\AdminUi\Symfony\Theme\HasThemeProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SyliusAdminUiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(dirname(__DIR__, 3) . '/config'),
        );

        $loader->load('services.php');

        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sylius_admin_ui.routing', $config['routing']);
        $container->setParameter('sylius_admin_ui.theme', $config['theme']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $theme = $config['theme'];
        $bundles = $container->getParameter('kernel.bundles');

        foreach ($bundles as $bundleClass) {
            if (!is_a($bundleClass, HasThemeProviderInterface::class, true)) {
                continue;
            }

            $bundle = new $bundleClass();
            $provider = $bundle->getThemeProvider();
            $reflection = new \ReflectionClass($provider);
            $attributes = $reflection->getAttributes(AsTheme::class);

            foreach ($attributes as $attribute) {
                $themeAttr = $attribute->newInstance();

                if ($themeAttr->name === $theme) {
                    $provider->loadConfiguration($container);

                    return;
                }
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No ThemeProvider found for theme "%s"', $theme),
        );
    }
}
