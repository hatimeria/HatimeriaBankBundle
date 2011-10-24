<?php

/**
 * This file is part of SillyCMS.
 *
 * (c) 2010 P'unk Avenue LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Tom Boutell <tom@punkave.com>
 * @package SillyCMS
 * @subpackage Twig
 */

namespace Hatimeria\BankBundle\Twig\Extensions;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Filter_Method;
use \Twig_Environment;
use \Twig_Extension;

class Currency extends Twig_Extension
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'currency_in_words' => new Twig_Filter_Method($this, 'currencyInWords', array('is_safe' => array('html'), 'pre_escape' => 'html')),
            'currency' => new Twig_Filter_Method($this, 'formatCurrency', array('is_safe' => array('html'), 'pre_escape' => 'html')),
        );
    }
    
    public function formatCurrency($number)
    {
        // @todo support different locales with money_format
        return number_format($number, 2, ",", " ");
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Currency';
    }

    public function currencyInWords($number)
    {
       return $this->container->get("bank.currency.util.numberwords")->toCurrency($number);
    }
}