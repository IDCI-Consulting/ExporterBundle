<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class ExportFactory
{
    static public function getInstance($format)
    {
        $className = sprintf('%s\%sExport',
            'IDCI\Bundle\ExporterBundle\Export',
            ucwords($format)
        );

        return new $className;
    }
}
