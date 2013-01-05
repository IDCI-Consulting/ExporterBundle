<?php

namespace IDCI\Bundle\ExporterBundle\Export;

class JsonpExport extends JsonExport
{
    public function buildHeader()
    {
        $callback = $this->parameters->get('callback');
        $this->setContent(sprintf('%s([', $callback));
    }

    public function buildFooter()
    {
        $this->setContent($this->getContent().']);');
    }
}
