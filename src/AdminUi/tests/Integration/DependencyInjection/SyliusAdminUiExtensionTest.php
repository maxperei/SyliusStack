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

namespace Tests\Sylius\AdminUi\Integration\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\AdminUi\Knp\Menu\MenuBuilder;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Sylius\AdminUi\Symfony\DependencyInjection\Configuration;
use Sylius\AdminUi\Symfony\DependencyInjection\SyliusAdminUiExtension;
use Sylius\AdminUi\TwigHooks\Hookable\Metadata\RoutingHookableMetadataFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tests\Sylius\AdminUi\Fixtures\AcmeAdminUi\AcmeAdminUiBundle;
use Tests\Sylius\AdminUi\Fixtures\BootstrapAdminUi\BootstrapAdminUiBundle;

final class SyliusAdminUiExtensionTest extends AbstractExtensionTestCase
{
    use ConfigurationTestCaseTrait;

    public function testItRegistersKnpMenuBuilder(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sylius_admin_ui.knp.menu_builder', MenuBuilder::class);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius_admin_ui.knp.menu_builder',
            'knp_menu.menu_builder',
            [
                'method' => 'createMenu',
                'alias' => 'sylius_admin_ui.menu.sidebar',
            ],
        )
        ;

        $this->assertContainerBuilderHasAlias(MenuBuilderInterface::class, 'sylius_admin_ui.knp.menu_builder');
    }

    public function testItRegistersTwigHooksFactoryHookableMetadata(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sylius_admin_ui.twig_hooks.factory.hookable_metadata', RoutingHookableMetadataFactory::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius_admin_ui.twig_hooks.factory.hookable_metadata',
            0,
            new Reference('.inner'),
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius_admin_ui.twig_hooks.factory.hookable_metadata',
            1,
            '%sylius_admin_ui.routing%',
        );
    }

    public function testItRegistersRoutingParameter(): void
    {
        $this->load([
            'routing' => [
                'login_path' => '/login',
                'logout_path' => '/logout',
                'dashboard_path' => '/admin',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius_admin_ui.routing', [
            'login_path' => '/login',
            'logout_path' => '/logout',
            'dashboard_path' => '/admin',
        ]);
    }

    public function testItThrowsAnErrorWhenTryingToRegisterNonStringRoutingPath(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'routing' => [
                        'login_path' => false,
                    ],
                ],
            ],
            'Invalid configuration for path "sylius_admin_ui.routing": Path must be a string. "bool" given.',
        );
    }

    public function testItRegistersDefaultThemeParameter(): void
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('sylius_admin_ui.theme', 'bootstrap');

        $this->load(['theme' => 'acme']);
        $this->assertContainerBuilderHasParameter('sylius_admin_ui.theme', 'acme');
    }

    public function testItThrowsAnErrorWhenTryingToRegisterEmptyThemeName(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'theme' => null,
                ],
            ],
            'The path "sylius_admin_ui.theme" cannot contain an empty value, but got null.',
        );
    }

    public function testItLoadsTheMatchingThemeProvider(): void
    {
        $container = $this->setUpContainer();

        $extension = new SyliusAdminUiExtension();
        $extension->prepend($container);

        $this->assertTrue($container->hasParameter('bootstrap_admin_ui.loaded'));

        $container = $this->setUpContainer();
        $container->prependExtensionConfig('sylius_admin_ui', ['theme' => 'acme']);

        $extension = new SyliusAdminUiExtension();
        $extension->prepend($container);

        $this->assertFalse($container->hasParameter('bootstrap_admin_ui.loaded'));
        $this->assertTrue($container->hasParameter('acme_admin_ui.loaded'));
    }

    public function testItThrowsIfThemeProviderIsNotRegistered(): void
    {
        $container = $this->setUpContainer();
        $container->prependExtensionConfig('sylius_admin_ui', ['theme' => 'unknown']);

        $extension = new SyliusAdminUiExtension();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No ThemeProvider found for theme "unknown"');

        $extension->prepend($container);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusAdminUiExtension(),
        ];
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    private function setUpContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', [
            'BootstrapAdminUiBundle' => BootstrapAdminUiBundle::class,
            'AcmeAdminUiBundle' => AcmeAdminUiBundle::class,
        ]);

        return $container;
    }
}
