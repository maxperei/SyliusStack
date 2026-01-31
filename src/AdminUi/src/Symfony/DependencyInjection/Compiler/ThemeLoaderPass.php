<?php

namespace Sylius\AdminUi\Symfony\DependencyInjection\Compiler;

use Sylius\AdminUi\Symfony\Theme\Attribute\AsTheme;
use Sylius\AdminUi\Symfony\Theme\ThemeProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ThemeLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius_admin_ui.theme')) {
            return;
        }

        $activeTheme = $container->getParameter('sylius_admin_ui.theme');
        $taggedServices = $container->findTaggedServiceIds(ThemeProviderInterface::class);

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();

            $reflection = new \ReflectionClass($class);
            $attributes = $reflection->getAttributes(AsTheme::class);

            foreach ($attributes as $attribute) {
                $themeAttr = $attribute->newInstance();

                if ($themeAttr->name === $activeTheme) {
                    /** @var ThemeProviderInterface $provider */
                    $provider = new $class();
                    $provider->loadConfiguration($container);

                    return;
                }
            }

            $container->removeDefinition($id);
        }
    }
}
