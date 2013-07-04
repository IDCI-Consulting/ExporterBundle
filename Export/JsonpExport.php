<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Export;

class JsonpExport extends JsonExport
{
    protected $contentType = 'application/javascript';

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
