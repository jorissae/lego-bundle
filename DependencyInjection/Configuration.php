<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
                ->scalarNode('route_login')->defaultValue('idk_lego_security_check')->end()
                ->scalarNode('route_logout')->defaultValue('idk_lego_security_logout')->end()
                ->scalarNode('service_menu')->defaultValue('Idk\LegoBundle\Service\Menu')->end()
                ->scalarNode('service_header')->defaultValue('Idk\LegoBundle\Service\Header')->end()
                ->scalarNode('service_footer')->defaultValue('Idk\LegoBundle\Service\Footer')->end()
                ->scalarNode('user_class')->defaultValue('App\Entity\User')->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
