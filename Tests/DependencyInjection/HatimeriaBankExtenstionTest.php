<?php

namespace Hatimeria\BankBundle\Test\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;

use Hatimeria\FrameworkBundle\Test\TestCase;

use Hatimeria\BankBundle\DependencyInjection\HatimeriaBankExtension;

class HatimeriaBankExtensionTest extends TestCase
{
    public function testResourcesLoading()
    {
        $path     = realpath(dirname(__FILE__) . '/../..') . '/Resources/config/services.xml';
        $resource = new FileResource($path);

        $extension = new HatimeriaBankExtension();

        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->atLeastOnce())
            ->method('addResource')
            ->with($resource);

        $extension->load(array(array('model_classes_path' => 'test')), $container);
    }
}