<?php
namespace Export\Service\ViewHelper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Export\View\Helper\ExportSelectedButton;

class ExportSelectedButtonFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $application = $serviceLocator->get('Application');

        $viewHelper = new ExportSelectedButton($application);

        return $viewHelper;
    }
}