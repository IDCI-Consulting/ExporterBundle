<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Export;

class IcsExport extends AbstractExport
{
    protected $contentType = 'text/ics';

    public function buildHeader()
    {
        $this->setContent('BEGIN:VCALENDAR'.PHP_EOL.'VERSION:2.0'.PHP_EOL.'PRODID:-//hacksw/handcal//NONSGML v1.0//EN'.PHP_EOL.PHP_EOL);
    }

    public function buildFooter()
    {
        $this->setContent($this->getContent().'END:VCALENDAR');
    }
}
