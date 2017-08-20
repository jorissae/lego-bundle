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
class IdkLegoExtension extends Extension// implements PrependExtensionInterface
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

        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
    }

    /*public function prepend(ContainerBuilder $container)
    {

        $parameterName = 'datePicker_startDate';

        $config = array();
        $config['globals'][$parameterName] = '01/01/1970';

        if($container->hasParameter($parameterName)) {
            $config['globals'][$parameterName] = $container->getParameter($parameterName);
        }

        $container->prependExtensionConfig('twig', $config);
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $liip = array(
            'filter_sets'=>array(
                'attachable_thumb'=>array(
                    'quality' => 100,
                    'filters' => array(
                        'thumbnail'=> array('size'=>array(100,100),'mode'=>'outbound','allow_upscale'=>true
        )))));
        $container->prependExtensionConfig('liip_imagine',$liip);

    }*/
}

