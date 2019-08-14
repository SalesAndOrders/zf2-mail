<?php
namespace Mailing;

use Zend\ModuleManager\Feature;

/**
 * Class Module
 * @package Mailing
 */
class Module implements Feature\ConfigProviderInterface, Feature\ServiceProviderInterface
{
    /**
     * @return array|mixed|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    /**
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
