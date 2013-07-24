<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Service;

use IDCI\Bundle\ExporterBundle\Export\ExportFactory;
use IDCI\Bundle\ExporterBundle\Exceptions\UndefinedExportableEntityException;

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
     * @param string $entityReference
     * @return array
     * @throw UndefinedExportableEntityException
     */
    public function getEntityReferenceConfiguration($entityReference)
    {
        $configuration = $this->container->getParameter('exporterConfiguration');
        if(isset($configuration['entities'][$entityReference])) {
            return $configuration['entities'][$entityReference];
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
     * @param string $entityReference
     * @return Repository
     */
    public function guessEntityRepository($entityReference)
    {
        $entityConfiguration = $this->getEntityReferenceConfiguration($entityReference);
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
     * @param string $entityReference
     * @param array $params
     * @return DoctrineCollection
     */
    public function extract($entityReference, $params = array())
    {
        $repository = $this->guessEntityRepository($entityReference);

        $reflectionClass = $this->getEntityReflectionClass($repository);
        if($reflectionClass->hasMethod('extract')) {
            return $repository->extract(self::cleanParams($params));
        }
        
        return $repository->findAll();
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
        $export = ExportFactory::getInstance(
            $format,
            self::cleanParams($params)
        );

        $export->buildHeader();
        foreach($entities as $entity) {
            $transformer = $this->guessTransformer($entity, $format);
            $export->addContent($transformer->transform($entity, $format));
        }
        $export->buildFooter();

        return $export;
    }

    /**
     * Clean Params
     *
     * @param array $params
     * @return array
     */
    static protected function cleanParams($params)
    {
        $clean = array();
        foreach($params as $k => $v) {
            if($v != '') {
                $clean[$k] = $v;
            }
        }

        return $clean;
    }
}
