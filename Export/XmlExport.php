<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class XmlExport extends AbstractExport
{
    protected $contentType = 'application/xml';

    public function buildHeader()
    {
        $this->addContent('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL);
        $this->addContent('<entities>'.PHP_EOL);
    }

    public function buildFooter()
    {
        $this->addContent('</entities>');
    }
}
