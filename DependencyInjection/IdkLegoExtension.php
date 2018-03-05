<?php

namespace Idk\LegoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IdkLegoExtension extends Extension implements PrependExtensionInterface
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

        $configuration = new Configuration();
        $processedConfig =  $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'lego.skin', $processedConfig[ 'skin' ] );
        $container->setParameter( 'lego.layout', $processedConfig[ 'layout' ] );
        $container->setParameter( 'lego.layout_login', $processedConfig[ 'layout_login' ] );
        $container->setParameter( 'lego.route.login', $processedConfig['route_login']);
        $container->setParameter( 'lego.route.logout', $processedConfig['route_logout']);
        $container->setParameter( 'lego.service.menu.class', $processedConfig[ 'service_menu_class' ] );
        $container->setParameter( 'lego.service.header.class', $processedConfig[ 'service_header_class' ] );
        $container->setParameter( 'lego.service.footer.class', $processedConfig[ 'service_footer_class' ] );

    }

    public function prepend(ContainerBuilder $container)
    {

        $config = [];
        $parameterName = 'lego_view';
        $config['globals'][$parameterName] = '@lego.service.globals_parameters_provider';
        if($container->hasParameter($parameterName)) {
            $config['globals'][$parameterName] = $container->getParameter($parameterName);
        }
        $container->prependExtensionConfig('twig', $config);

    }
}

