<?php
namespace Export\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ListController extends AbstractActionController
{
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function indexAction()
    {
        $view = new ViewModel;
        $directoryPath = '~/omeka-s/files/CSV_Export';
        $downloads = scandir($directoryPath);
        $view->setVariable('downloads', $downloads);

        $plop = "hello world";
        $view->setVariable('plop', $plop);

        return $view;
    }
}
