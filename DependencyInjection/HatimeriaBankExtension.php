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
        
        $this->updateParameters($config, $container, 'hatimeria_bank.');
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        $subscriptions = isset($configs[0]["subscriptions"]["variants"]) ? 
            $configs[0]["subscriptions"]["variants"] : $config["subscriptions"]["variants"];
        
        $container->setParameter("hatimeria_bank.subscription.variants", $subscriptions);
        
        $virtuals = isset($configs[0]["virtual_packages"]) ? $configs[0]["virtual_packages"] : $config['virtual_packages'];
        
        $container->setParameter("hatimeria_bank.currency.virtual.packages", $virtuals);
        
        foreach (array('services') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if (in_array('FOS\UserBundle\FOSUserBundle', $container->getParameter('kernel.bundles'))) {

            $fos_class = $container->getParameterBag()->resolveValue($container->getParameterBag()->get('fos_user.model.user.class'));

            $builder = $container->getDefinition('bank');
            $builder->replaceArgument(4, $fos_class);
        }
    }
    
    public function updateParameters($config, ContainerBuilder $container, $ns = '')
    {
        foreach ($config as $key => $value)
        {
            if (is_array($value) && count($value))  {
                $this->updateParameters($value, $container, $ns . $key . '.');
            } else {
                $container->setParameter($ns . $key, $value);
            }
        }
    }    
}
