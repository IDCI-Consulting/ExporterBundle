<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Export;

class JsonExport extends AbstractExport
{
    protected $contentType = 'application/json';

    public function buildHeader()
    {
        $this->setContent('[');
    }

    public function buildFooter()
    {
        $this->setContent($this->getContent().']');
    }

    public function addContent($content)
    {
        $glue = $this->getCount() > 0 ? ',' : '';
        parent::addContent($glue.$content);
    }
}
