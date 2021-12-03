<?php

declare(strict_types=1);

namespace AppBundle;

use Symfony\Component\{
    DependencyInjection\ContainerBuilder,
    Config\FileLocator,
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\Loader
};

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.xml');
    }
}
