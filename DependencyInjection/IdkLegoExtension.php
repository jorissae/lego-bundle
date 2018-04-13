<?php

namespace Idk\LegoBundle\DependencyInjection;

use Idk\LegoBundle\DependencyInjection\Compiler\ComponentPass;
//use Idk\LegoBundle\DependencyInjection\Compiler\WidgetPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

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
            array('IdkLegoBundle:Form:lego_widget.html.twig'),
            $container->getParameter('twig.form.resources')
            ));
        }


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('components.yml');
        $loader->load('widgets.yml');

        $configuration = new Configuration();
        $processedConfig =  $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'lego.skin', $processedConfig[ 'skin' ] );
        $container->setParameter( 'lego.layout', $processedConfig[ 'layout' ] );
        $container->setParameter( 'lego.layout_login', $processedConfig[ 'layout_login' ] );
        $container->setParameter( 'lego.route.login', $processedConfig['route_login']);
        $container->setParameter( 'lego.route.logout', $processedConfig['route_logout']);
        $container->setParameter( 'lego.service.menu', $processedConfig[ 'service_menu' ] );
        $container->setParameter( 'lego.service.header', $processedConfig[ 'service_header' ] );
        $container->setParameter( 'lego.service.footer', $processedConfig[ 'service_footer' ] );
        $container->setParameter( 'lego.user.class', $processedConfig[ 'user_class' ] );




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

