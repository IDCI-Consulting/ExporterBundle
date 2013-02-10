<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Service;

use IDCI\Bundle\ExporterBundle\Export\ExportFactory;
use IDCI\Bundle\ExporterBundle\Exceptions\UndefinedExportableEntityException;
use IDCI\Bundle\ExporterBundle\Exceptions\MissingRepositoryExtractFunctionException;

class Manager
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * getEntityReferenceConfiguration
     *
     * @param string $entity_reference
     * @return array
     * @throw UndefinedExportableEntityException
     */
    public function getEntityReferenceConfiguration($entity_reference)
    {
        $configuration = $this->container->getParameter('exporterConfiguration');
        if(isset($configuration['entities'][$entity_reference])) {
            return $configuration['entities'][$entity_reference];
        }

        throw new UndefinedExportableEntityException();
    }

    /**
     * getEntityConfiguration
     *
     * @param string $entity
     * @return array
     * @throw UndefinedExportableEntityException
     */
    public function getEntityConfiguration($entity)
    {
        $configuration = $this->container->getParameter('exporterConfiguration');
        $reflectionClass = $this->getEntityReflectionClass($entity);
        foreach($configuration['entities'] as $entityConfiguration) {
            if($entityConfiguration['class'] == $reflectionClass->getName()) {
                return $entityConfiguration;
            }
        }
        throw new UndefinedExportableEntityException();
    }

    /**
     * getEntityTransformerConfiguration
     *
     * @param string $entity
     * @param string $format
     * @return array
     */
    public function getEntityTransformerConfiguration($entity, $format)
    {
        $entityConfiguration = $this->getEntityConfiguration($entity);
        $formats = $entityConfiguration['formats'];

        if(isset($formats[$format]) && isset($formats[$format]['transformer'])) {
            return $formats[$format]['transformer'];
        }

        return array();
    }

    /**
     * getEntityTransformerOptions
     *
     * @param string $entity
     * @param string $format
     * @return array
     */
    public function getEntityTransformerOptions($entity, $format)
    {
        $transformerConfiguration = $this->getEntityTransformerConfiguration($entity, $format);

        if(isset($transformerConfiguration['options'])) {
            return $transformerConfiguration['options'];
        }

        return array();
    }

    /**
     * guessEntityRepository
     *
     * @param string $entity_reference
     * @return Repository
     */
    public function guessEntityRepository($entity_reference)
    {
        $entityConfiguration = $this->getEntityReferenceConfiguration($entity_reference);
        $class = $entityConfiguration['class'];
        $em = $this->getEntityManager();

        return $em->getRepository($class);
    }

    /**
     * Is a proxy class
     *
     * @param ReflectionClass $reflection
     * @return boolean
     */
    public static function isProxyClass(\ReflectionClass $reflection)
    {
        return in_array('Doctrine\ORM\Proxy\Proxy', array_keys($reflection->getInterfaces()));
    }

    /**
     * getEntityReflectionClass
     *
     * @param Object $entity
     * @return ReflectionClass
     */
    public function getEntityReflectionClass($entity)
    {
        $reflection = new \ReflectionClass($entity);
        if(self::isProxyClass($reflection) && $reflection->getParentClass()) {
            return $reflection->getParentClass();
        }

        return $reflection;
    }

    /**
     * extract
     *
     * @param string $entity_reference
     * @param array $params
     * @return DoctrineCollection
     */
    public function extract($entity_reference, $params = array())
    {
        $repository = $this->guessEntityRepository($entity_reference);

        $reflectionClass = $this->getEntityReflectionClass($repository);
        if($reflectionClass->hasMethod('extract')) {
            return $repository->extract($params);
        }

        throw new MissingRepositoryExtractFunctionException();
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
        $transformerService = 'idci_exporter.transformer_twig';

        $transformerConfiguration = $this->getEntityTransformerConfiguration($entity, $format);
        if($transformerConfiguration && isset($transformerConfiguration['service'])) {
            $transformerService = $transformerConfiguration['service'];
        }

        return $this->container->get($transformerService);
    }

    /**
     * export
     *
     * @param DoctrineCollection $entities
     * @param string $format
     * @param array $params
     * @return AbstractExport
     */
    public function export($entities, $format, $params = array())
    {
        $export = ExportFactory::getInstance($format, $params);

        $export->buildHeader();
        foreach($entities as $entity) {
            $transformer = $this->guessTransformer($entity, $format);
            $export->addContent($transformer->transform($entity, $format));
        }
        $export->buildFooter();

        return $export;
    }
}
