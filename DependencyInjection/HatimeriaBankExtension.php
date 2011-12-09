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

        $smsConfiguration = $config['sms_configuration'];

        unset($config['sms_configuration']);

        $container->setParameter('hatimeria_bank.sms_configuration', $smsConfiguration);

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
