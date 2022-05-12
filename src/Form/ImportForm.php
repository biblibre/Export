<?php
namespace Export\Form;

use Omeka\Form\Element\ItemSetSelect;
use Laminas\Form\Form;

class ImportForm extends Form
{
    public function init()
    {
        //$this->setAttribute('action', 'export/job');
        //$this->setAttribute('method', 'post');
        $this->add([
                    'name' => 'item_set',
                    'type' => ItemSetSelect::class,
                    'attributes' => [
                        'id' => 'select-item-set',
                        'class' => 'chosen-select',
                        'multiple' => false,
                        'data-placeholder' => 'Select item sets', // @translate
                    ],
                    'options' => [
                        'label' => 'Select item set to export', // @translate
                        'resource_value_options' => [
                            'resource' => 'item_sets',
                            'query' => [],
                        ],
                    ],
        ]);
    }
}
