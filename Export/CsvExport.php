<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Export;

class CsvExport extends AbstractExport
{
    protected $contentType = 'text/csv';

    /**
     * addContent
     *
     * @param string $content
     */
    public function addContent($content)
    {
        $content = trim(preg_replace('/\s+/', ' ', $content));
        parent::addContent($content.PHP_EOL);
    }
}
