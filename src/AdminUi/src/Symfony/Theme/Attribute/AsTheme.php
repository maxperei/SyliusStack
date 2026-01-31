<?php

namespace Sylius\AdminUi\Symfony\Theme\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTheme
{
    public function __construct(
        public readonly string $name,
        public readonly string $priority,
    ) {}
}
