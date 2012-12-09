<?php

namespace IDCI\Bundle\ExporterBundle\Service;

class Manager
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
