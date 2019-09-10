<?php
namespace Mailing;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class Factory
 * @package Mailing\Service
 */
class Factory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param string $requestedName
     * @param array|null $options
     * @return Service|object
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $config = $locator->get('config');
        $emailConfig = $config['mail'];
        $emailConfig['transport'] = isset($config['transport']) ? $config['transport'] : [];

        $renderer = $locator->get('ViewRenderer');
        return new Service(new Config($emailConfig), $renderer);
    }
}
