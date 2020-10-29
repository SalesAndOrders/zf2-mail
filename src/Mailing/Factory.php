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
        return new Service(new Config($emailConfig, $this->getTranslator($locator, $emailConfig)), $renderer);
    }

    /**
     * @param ContainerInterface $locator
     * @param $config
     * @return |null
     */
    public function getTranslator(ContainerInterface $locator, $config)
    {
        $translator = !empty($config['translator']) ? class_exists($config['translator'])  : null;
        if (!empty($config['translator'])) {
            if (class_exists($config['translator'])) {
                $translator = new $config['translator']();
                return $translator->getTranslator();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
