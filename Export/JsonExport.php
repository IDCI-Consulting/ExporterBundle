<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class JsonExport extends AbstractExport
{
    protected $contentType = 'application/json';

    public function buildHeader()
    {
        $this->addContent('{'.PHP_EOL);
    }

    public function buildFooter()
    {
        $this->addContent('}');
    }
}
