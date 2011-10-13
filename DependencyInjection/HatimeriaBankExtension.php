<?php

namespace Hatimeria\BankBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class HatimeriaBankExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        
        foreach ($config as $key => $value)
        {
            $container->setParameter('hatimeria_bank.'.$key, $value);
        }        
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('services') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
    }
}
