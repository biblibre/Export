<?php

namespace Export\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\Application;

class ExportButton extends AbstractHelper
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
        $routeMatch = $mvcEvent->getRouteMatch();
        $params = $routeMatch->getParams();

        $view = $this->getView();

        if (array_key_exists('id', $params)) {
            $query = ['id' => $params['id']];
        } else {
            $query = $request->getQuery()->toArray();
        }

        $url = $view->url('export/download', [], ['query' => $query ], true);

        return '<a href="' . $url . '"><button id="export_button">Export</button></a>'; //@translate
    }
}
