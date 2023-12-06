<?php
/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2023 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\ConseilGouz\\Module\\Switcheditor'));
        $container->registerServiceProvider(new HelperFactory('\\ConseilGouz\\Module\\Switcheditor\\Administrator\\Helper'));

        $container->registerServiceProvider(new Module());
    }
};
