<?php
namespace Export\Controller;

use Export\Form\ImportForm;
use Export\Job\ExportJob;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Stdlib\Message;

class IndexController extends AbstractActionController
{
    protected $serviceLocator;
    protected $jobId;
    protected $jobUrl;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function exportAction()
    {
        $view = new ViewModel;
        $form = $this->getForm(ImportForm::class);
        $view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                $args['query'] = ['item_set_id' => $formData['item_set']];
                $this->sendJob($args);

                $message = new Message(
                    'Export started in %sjob %s%s', // @translate
                    sprintf('<a href="%s">', htmlspecialchars($this->getJobUrl(),
                )),
                    $this->getJobId(),
                    '</a>'
                );

                $message->setEscapeHtml(false);
                $this->messenger()->addSuccess($message);

                return $this->redirect()->toRoute(null, [], [], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        return $view;
    }

    public function downloadAction()
    {
        $exporter = $this->serviceLocator->get('Export\Exporter');
        $request = $this->getRequest();
        $postParams = $request->getPost();
        $queryParams = $request->getQuery();

        if ($postParams['resource_ids'] || $queryParams['id']) {
            $csvTemp = tmpfile();
            $exporter->setFileHandle($csvTemp);

            if ($postParams['resource_ids']) {
                $exporter->downloadSelected($postParams['resource_ids']);
            } else {
                $exporter->downloadOne($queryParams['id']);
            }
            fseek($csvTemp, 0);
            $rows = '';
            while (! feof($csvTemp)) {
                $rows .= fread($csvTemp, 1024);
            }
            fclose($csvTemp);

            $response = $this->getResponse();
            $response->setContent($rows);
            $response->getHeaders()->addHeaderLine('Content-type', 'text/csv');
            $response->getHeaders()->addHeaderLine('Content-Disposition', 'attachment; filename="omekas_export.csv"');

            return $response;
        } else {
            $args = $queryParams->toArray();
            unset($args['page']);

            $this->sendJob(['query' => $args]);

            $message = new Message(
                'Export started', // @translate
            );

            $message->setEscapeHtml(false);
            $this->messenger()->addSuccess($message);

            return $this->redirect()->toRoute('admin/id', ['controller' => 'job', 'action' => 'show', 'id' => $this->getJobId()], []);
        }
    }

    protected function sendJob($args)
    {
        $job = $this->jobDispatcher()->dispatch(ExportJob::class, $args);

        $jobUrl = $this->url()->fromRoute('admin/id', [
                    'controller' => 'job',
                    'action' => 'show',
                    'id' => $job->getId(),
                ]);

        $this->setJobId($job->getId());
        $this->setJobUrl($jobUrl);
    }

    protected function getJobId()
    {
        return $this->jobId;
    }

    protected function setJobId($id)
    {
        $this->jobId = $id;
    }

    protected function getJobUrl()
    {
        return $this->jobUrl;
    }

    protected function setJobUrl($url)
    {
        $this->jobUrl = $url;
    }
}
