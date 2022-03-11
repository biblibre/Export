<?php
namespace Export\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ListController extends AbstractActionController
{
    public function listAction()
    {
        $view = new ViewModel;
        $directoryPath = 'files/CSV_Export';
        $downloads = array_diff(scandir($directoryPath, SCANDIR_SORT_DESCENDING), ['..', '.']);
        $view->setVariable('downloads', $downloads);

        return $view;
    }

    public function deleteAction()
    {
        $query = $this->getRequest()->getQuery();
        unlink($query['filePath']);
        return $this->redirect()->toRoute('admin/export/list', ['controller' => 'list', 'action' => 'list'], []);
    }
}
