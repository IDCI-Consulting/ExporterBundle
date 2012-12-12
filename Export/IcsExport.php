<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class IcsExport extends AbstractExport
{
    protected $contentType = 'text/ics';
    
    public function buildHeader()
    {
        $this->addContent('BEGIN:VCALENDAR'.PHP_EOL);
        $this->addContent('VERSION:2.0'.PHP_EOL);
        $this->addContent('PRODID:-//hacksw/handcal//NONSGML v1.0//EN'.PHP_EOL.PHP_EOL);
    }

    public function buildFooter()
    {
        $this->addContent('END:VCALENDAR');
    }
}

