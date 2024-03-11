<?php

namespace Export\Form;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Checkbox;

class SiteSettingsFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('export');
        $this->setLabel('Export'); // @translate

        $this->add([
            'type' => Checkbox::class,
            'name' => 'export_show_after_item',
            'options' => [
                'label' => 'Show on item page', // @translate
                'element_group' => 'export',
            ],
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'export_show_after_media',
            'options' => [
                'label' => 'Show on media page', // @translate
                'element_group' => 'export',
            ],
        ]);
    }
}