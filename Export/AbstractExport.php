<?php

namespace IDCI\Bundle\ExporterBundle\Export;

abstract class AbstractExport
{
    protected $content;
    protected $contentType;
    protected $count;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCount(0);
    }

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
     * getCount
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * setCount
     *
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
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
            $this->setContent($this->getContent().$content);
            $this->setCount($this->getCount()+1);
        } catch(\Exception $e) {
            die('TODO: throw an exception if $content can\'t be concateneted');
        }
    }
}