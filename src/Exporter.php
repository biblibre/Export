<?php

namespace Export;

class Exporter
{
    protected $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function downloadOne($query)
    {
        $services = $this->application->getServiceManager();
        $api = $services->get('Omeka\ApiManager');

        $item[] = $api->search('items', ['id' => $query])->getContent()[0];

        $this->transformToCSV($item);
    }

    public function downloadSelected($query)
    {
        $services = $this->application->getServiceManager();
        $api = $services->get('Omeka\ApiManager');

        $itemsId = $query;
        foreach ($itemsId as $itemId) {
            $items[] = $api->search('items', ['id' => $itemId])->getContent()[0];
        }

        $this->transformToCSV($items);
    }

    public function exportItemSet($query)
    {
        $services = $this->application->getServiceManager();
        $api = $services->get('Omeka\ApiManager');

        $itemSetId = $query;
        $items = $api->search('items', ['item_set_id' => $itemSetId])->getContent();

        $this->transformToCSV($items);
    }

    public function exportQuery($query)
    {
        $services = $this->application->getServiceManager();
        $api = $services->get('Omeka\ApiManager');

        $items = $api->search('items', $query)->getContent();

        $this->transformToCSV($items);
    }

    protected function transformToCSV($items)
    {
        $items = $this->formatData($items);
        $itemMedia = [];
        foreach ($items as $item) {
            if (array_key_exists('o:media', $item) && !empty($item['o:media'])) {
                $mediaIds = $item['o:media'];
                $mediaOut = "";
                $mediaJson = "";
                foreach ($mediaIds as $mediaId) {
                    $id = $mediaId['o:id'];
                    $media = $this->getData($id, 'id', 'media');
                    foreach ($media as $medium) {
                        $mediaOut = $mediaOut . $medium['o:filename'] . ";";
                        $mediaJson = $mediaJson . json_encode($medium) . ";";
                        $item['media:link'] = $mediaOut;
                        $item['media:full'] = $mediaJson;
                    }
                }
            } else {
                $item['media:link'] = "";
                $item['media:full'] = "";
            }
            array_push($itemMedia, $item);
        }

        $properties = $this->getData("", 'term', 'properties');
        $propertyNames = [];
        foreach ($properties as $property) {
            $p = $property['o:term'];
            array_push($propertyNames, $p);
        }
        $collection = $itemMedia;
        $properties = $propertyNames;

        $output = $this->getFileHandle();

        $resultCount = sizeOf($collection);
        if ($resultCount > 0) {
            $collectionHeaders = array_keys($collection[0]);
            $header = array_merge($collectionHeaders, $properties);
            fputcsv($output, $header);
            foreach ($collection as $item) {
                if (is_array($item)) {
                    $outputItem = [];
                    foreach ($header as $column) {
                        if (array_key_exists($column, $item)) {
                            $row = $item[$column];
                            if (is_array($row)) {
                                if (array_key_exists('o:id', $row)) {
                                    array_push($outputItem, $row['o:id']);
                                } elseif (array_key_exists('@value', $row)) {
                                    array_push($outputItem, $row['@value']);
                                } else {
                                    //Row has multiple values
                                    $multiRow = "";
                                    foreach ($row as $single) {
                                        if (is_array($single)) {
                                            if (array_key_exists('o:id', $single)) {
                                                $multiRow = $multiRow . ";" . $single['o:id'] ;
                                            } elseif (array_key_exists('@value', $single)) {
                                                $multiRow = $multiRow . ";" . $single['@value'] ;
                                            } elseif (array_key_exists('@id', $single)) {
                                                $multiRow = $multiRow . ";" . $single['@id'] ;
                                            }
                                        } else {
                                            $multiRow = $multiRow . ";" . $single ;
                                        }
                                    }
                                    $multiRow = substr($multiRow, 1);
                                    array_push($outputItem, $multiRow);
                                }
                            } else {
                                array_push($outputItem, $row);
                            }
                        } else {
                            array_push($outputItem, "");
                        }
                    }
                    unset($item['media:full']);
                    array_push($outputItem, json_encode($item));
                    fputcsv($output, $outputItem);
                }
            }
        }
    }

    protected function getData($criteria, $field, $type)
    {
        $services = $this->application->getServiceManager();
        $api = $services->get('Omeka\ApiManager');

        $query[$field] = $criteria;
        $items = $api->search($type, $query)->getContent();
        $out = $this->formatData($items);

        return $out;
    }

    protected function formatData($rawData)
    {
        $arr = json_encode($rawData, true);
        $items = json_decode($arr, true);
        return $items ;
    }

    public function setFileHandle($fileHandle)
    {
        $this->fileHandle = $fileHandle;
    }

    public function getFileHandle()
    {
        return $this->fileHandle;
    }
}
