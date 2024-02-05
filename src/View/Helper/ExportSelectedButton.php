<?php

namespace Export\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\Application;

class ExportSelectedButton extends AbstractHelper
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    public function __invoke()
    {
        $mvcEvent = $this->application->getMvcEvent();
        $request = $mvcEvent->getRequest();

        $view = $this->getView();
        $query = $request->getQuery()->toArray();
        $url = $view->url('export/download', [], ['query' => $query], true);

        return '<button id="export-selected-button" style="margin-left:10px; display:none" form="batch-form" formaction="' . $url . '">Export selected</button>';
    }
}
   
