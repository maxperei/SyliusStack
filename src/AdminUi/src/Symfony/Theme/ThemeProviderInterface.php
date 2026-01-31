<?php

namespace Sylius\AdminUi\Symfony\Theme;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[AutoconfigureTag]
interface ThemeProviderInterface
{
    public function loadConfiguration(ContainerBuilder $container): void;
}
