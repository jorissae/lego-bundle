<?php

namespace Idk\LegoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('idk_lego');
        $rootNode
            ->children()
                ->scalarNode('skin')->defaultValue('skin-blue')->end()
                ->scalarNode('layout')->defaultValue('IdkLegoBundle:Layout:lego.html.twig')->end()
                ->scalarNode('layout_login')->defaultValue('IdkLegoBundle:Layout:lego_login.html.twig')->end()
                ->scalarNode('route_login')->defaultValue('fos_user_security_check')->end()
                ->scalarNode('route_logout')->defaultValue('fos_user_security_logout')->end()
                ->scalarNode('service_menu_class')->defaultValue('Idk\LegoBundle\Service\Menu')->end()
                ->scalarNode('service_header_class')->defaultValue('Idk\LegoBundle\Service\Header')->end()
                ->scalarNode('service_footer_class')->defaultValue('Idk\LegoBundle\Service\Footer')->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
