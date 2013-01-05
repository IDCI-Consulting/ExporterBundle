<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class ExportFactory
{
    /**
     * Create a new instance
     *
     * @param string $format
     * @param array $params
     * @return Export
     */
    static public function getInstance($format, $params = array())
    {
        $className = sprintf('%s\%sExport',
            'IDCI\Bundle\ExporterBundle\Export',
            ucwords($format)
        );

        return new $className($params);
    }
}
