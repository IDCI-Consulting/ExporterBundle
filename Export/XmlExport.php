<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class XmlExport extends AbstractExport
{
    protected $contentType = 'application/xml';

    public function buildHeader()
    {
        $this->setContent('<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<entities>'.PHP_EOL);
    }

    public function buildFooter()
    {
        $this->setContent($this->getContent().'</entities>');
    }
}
