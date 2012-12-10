<?php

namespace IDCI\Bundle\ExporterBundle\Service;

use IDCI\Bundle\ExporterBundle\Export\ExportFactory;

class Manager
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * guessTransformer
     *
     * @param Object $entity
     * @param string $format
     * @return Transformer
     */
    public function guessTransformer($entity, $format)
    {
        // By default
        $transformService = 'idci_exporter.transformer_twig';

        $configuration = $this->container->getParameter('entitiesConfiguration');
        if(isset($configuration[get_class($entity)]['formats'][$format])) {
            $formatConfiguration = $configuration[get_class($entity)]['formats'][$format];
            $transformService = $formatConfiguration['transformer'];
        }

        return $this->container->get($transformService);
    }

    /**
     * export
     *
     * @param DoctrineCollection $entities
     * @param string $format
     * @return ExportResult
     */
    public function export($entities, $format)
    {
        $export = ExportFactory::getInstance($format);

        $export->buildHeader();
        foreach($entities as $entity) {
            $transformer = $this->guessTransformer($entity, $format);
            $export->addContent($transformer->transform($entity, $format));
        }
        $export->buildFooter();

        return $export;
    }
}
