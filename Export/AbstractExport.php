<?php

namespace IDCI\Bundle\ExporterBundle\Export;

abstract class AbstractExport
{
    protected $content;
    protected $contentType;

    /**
     * getContent
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * setContent
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * getContentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * buildHeader
     */
    public function buildHeader() {}

    /**
     * buildFooter
     */
    public function buildFooter() {}

    /**
     * addContent
     *
     * @param string $content
     */
    public function addContent($content)
    {
        try {
            $this->setContent($this->getContent().PHP_EOL.$content);
        } catch(\Exception $e) {
             die('TODO: throw an exception if $content can\'t be concateneted');
        }
    }
}
