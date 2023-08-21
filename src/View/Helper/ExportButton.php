<?php

namespace Export\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\Application;
use Export\view\Helper\ExportButton as OriginalButton;

class ExportButton extends AbstractHelper
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function __invoke($exportSelection = false)
    {
        $mvcEvent = $this->application->getMvcEvent();
        $request = $mvcEvent->getRequest();
        $routeMatch = $mvcEvent->getRouteMatch();

        $route = $routeMatch->getMatchedRouteName();
        $params = $routeMatch->getParams();

        $view = $this->getView();
        $onResultPage = true;

        $isAdminController = in_array($params['controller'], ['Omeka\Controller\Admin\Item', 'Omeka\Controller\Admin\Media']);
        $isSiteController = in_array($params['controller'], ['Omeka\Controller\Site\Item', 'Omeka\Controller\Site\Media']);
        $isShowAction = $params['action'] === 'show' ? true : null;

        if (
            (
                ($route === 'admin/id' && $isAdminController) ||
                ($route === 'site/resource-id' && $isSiteController)
            ) &&
            isset($isShowAction)
        ) {
            $query = [
                'id' => $params['id'],
            ];
            $onResultPage = false;
        } else {
            $query = $request->getQuery()->toArray();
        }

        if ($isAdminController) {
            $url = $view->url('admin/export/download', [], ['query' => $query + ['exportSelection' => $exportSelection]], true);
        } else if ($isSiteController) {
            $url = $view->url('site/export/download', [], ['query' => $query + ['exportSelection' => $exportSelection]], true);
        }

        if ($onResultPage && $isAdminController) {
            return '<button id="exportcsvbutton" form="batch-form" formaction="' . $url . '"></button>';
        } else {
            return '<a href="' . $url . '"><button id="exportcsvbutton">Export CSV</button></a>';
        }
    }
}
