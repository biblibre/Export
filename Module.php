<?php
namespace Export;

use Laminas\EventManager\SharedEventManagerInterface;
use Omeka\Module\AbstractModule;
use Omeka\Form\SiteSettingsForm;
use Export\Form\SiteSettingsFieldset;
use Laminas\EventManager\Event;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Item',
            'view.browse.before',
            [$this, 'echoExportButtonHtml']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Item',
            'view.show.sidebar',
            [$this, 'echoExportButtonHtml']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.show.sidebar',
            [$this, 'echoExportButtonHtml']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Site\Item',
            'view.show.after',
            [$this, 'echoExportButtonHtml']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Site\Media',
            'view.show.after',
            [$this, 'echoExportButtonHtml']
        );
        $sharedEventManager->attach(
            SiteSettingsForm::class,
            'form.add_elements',
            [$this, 'onSiteSettingsFormAddElements']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Item',
            'view.browse.before',
            [$this, 'addAdminExportJs']
        );
    }
    public function echoExportButtonHtml($event)
    {
        $view = $event->getTarget();
        echo $view->partial('export/exportbutton');
    }
    public function onSiteSettingsFormAddElements($event)
    {
        $services = $this->getServiceLocator();
        $forms = $services->get('FormElementManager');
        $siteSettings = $services->get('Omeka\Settings\Site');

        $fieldset = $forms->get(SiteSettingsFieldset::class);
        $fieldset->populateValues([
            'export_show_after_item' => $siteSettings->get('export_show_after_item'),
            'export_show_after_media' => $siteSettings->get('export_show_after_media'),
        ]);
        $form = $event->getTarget();
        $groups = $form->getOption('element_groups');
        if (isset($groups)) {
            $groups['export'] = $fieldset->getLabel();
            $form->setOption('element_groups', $groups);
            foreach ($fieldset->getElements() as $element) {
                $form->add($element);
            }
        } else {
            $form->add($fieldset);
        }
    }
    public function addAdminExportJs(Event $event): void
    {
        $view = $event->getTarget();
        $view->headScript()->appendFile($view->assetUrl('js/export-button.js', 'Export'), 'text/javascript', ['defer' => 'defer']);
    }
}
