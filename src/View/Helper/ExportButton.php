<?php

namespace Export\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\Application;

class ExportButton extends AbstractHelper
{
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function __invoke()
    {
        $mvcEvent = $this->application->getMvcEvent();
        $request = $mvcEvent->getRequest();
        $routeMatch = $mvcEvent->getRouteMatch();

        $route = $routeMatch->getMatchedRouteName();
        $params = $routeMatch->getParams();

        $view = $this->getView();
        $onResultPage = true;

        if ($route === 'admin/id' && ($params['controller'] === 'Omeka\Controller\Admin\Item' && $params['action'] === 'show')) {
            $query = [
            'id' => $params['id'],
           ];
            $onResultPage = false;
        } else {
            $query = $request->getQuery()->toArray();
        }

        $url = $view->url('admin/export/download', [], ['query' => $query]);

        if ($onResultPage) {
            return '<button form="batch-form" formaction="' . $url . '">Export CSV</button>';
        } else {
            return '<a href="' . $url . '"><button>Export CSV</button></a>';
        }
    }
}
