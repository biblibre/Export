<?php

namespace Export\Job;

use Omeka\Job\AbstractJob;

class ExportJob extends AbstractJob
{
    public function perform()
    {
        $services = $this->getServiceLocator();
        $logger = $services->get('Omeka\Logger');
        $store = $this->getServiceLocator()->get('Omeka\File\Store');

        $exporter = $services->get('Export\Exporter');

        $logger->info('Job started');

        $now = date("Y-m-d_H-i-s");

        $filename = tempnam(sys_get_temp_dir(), 'omekas_export');
        $csvTemp = fopen($filename, 'w');
        $exporter->setFileHandle($csvTemp);

        $exporter->exportQuery($this->getArg('query'));

        fclose($csvTemp);

        $store->put($filename, "CSV_Export/omekas_$now.csv");

        unlink($filename);

        $logger->info("Saved in files/CSV_Export/omekas_$now.csv");
        $logger->info('Job ended');
    }
}
