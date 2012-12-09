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
     * @return Transformer
     */
    public function guessTransformer($entity)
    {
        $defaultTransformerService = 'idci_exporter.transformer_twig';

        return $this->container->get($defaultTransformerService);
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
            $transformer = $this->guessTransformer($entity);
            $export->addContent($transformer->transform($entity, $format));
        }
        $export->buildFooter();

        return $export;
    }
}
