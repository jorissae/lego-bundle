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

use Idk\LegoBundle\Component\BrickInterface;
use Idk\LegoBundle\DependencyInjection\Compiler\ComponentPass;
//use Idk\LegoBundle\DependencyInjection\Compiler\WidgetPass;
use Idk\LegoBundle\EditInPlaceType\EipTypeInterface;
use Idk\LegoBundle\Service\BulkActionInterface;
use Idk\LegoBundle\Service\RightBar\RightBarInterface;
use Idk\LegoBundle\Widget\WidgetInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Idk\LegoBundle\FilterType\FilterTypeInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IdkLegoExtension extends Extension implements ExtensionInterface, PrependExtensionInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {


        if ($container->hasParameter('twig.form.resources')) {
            $container->setParameter('twig.form.resources', array_merge(
            array('@IdkLego/Form/lego_widget.html.twig'),
            $container->getParameter('twig.form.resources')
            ));
        }


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('form.yaml');
        $loader->load('filter.yaml');
        $loader->load('components.yaml');
        $loader->load('widgets.yaml');
        $loader->load('actions.yaml');
        $loader->load('bulk.yaml');

        $configuration = new Configuration();
        $processedConfig =  $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'lego.skin', $processedConfig[ 'skin' ] );
        $container->setParameter( 'lego.favicon', $processedConfig[ 'favicon' ] );
        $container->setParameter( 'lego.layout', $processedConfig[ 'layout' ] );
        $container->setParameter( 'lego.layout_login', $processedConfig[ 'layout_login' ] );
        $container->setParameter( 'lego.route.login', $processedConfig['route_login']);
        $container->setParameter( 'lego.route.logout', $processedConfig['route_logout']);
        $container->setParameter( 'lego.service.menu', $processedConfig[ 'service_menu' ] );
        $container->setParameter( 'lego.service.header', $processedConfig[ 'service_header' ] );
        $container->setParameter( 'lego.service.footer', $processedConfig[ 'service_footer' ] );
        $container->setParameter( 'lego.user.class', $processedConfig[ 'user_class' ] );
        $container->setParameter( 'lego.locales', $processedConfig[ 'locales' ] );
        $container->setParameter( 'lego.default_locale', $processedConfig[ 'default_locale' ] );

        $container->registerForAutoconfiguration(FilterTypeInterface::class)->addTag('lego.filter');
        $container->registerForAutoconfiguration(BrickInterface::class)->addTag('lego.component');
        $container->registerForAutoconfiguration(RightBarInterface::class)->addTag('lego.right_bar');
        $container->registerForAutoconfiguration(BulkActionInterface::class)->addTag('lego.bulk_action');
       // $container->registerForAutoconfiguration(EipTypeInterface::class)->addTag('lego.eip');
        $container->registerForAutoconfiguration(WidgetInterface::class)->addTag('lego.widget');
        //exporter and batch and sidebar



    }

    public function prepend(ContainerBuilder $container)
    {
        $config = [];
        $parameterName = 'lego_view';
        $config['globals'][$parameterName] = '@Idk\LegoBundle\Service\GlobalsParametersProvider';
        if($container->hasParameter($parameterName)) {
            $config['globals'][$parameterName] = $container->getParameter($parameterName);
        }
        $container->prependExtensionConfig('twig', $config);

    }
}

