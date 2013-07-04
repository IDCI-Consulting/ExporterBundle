<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Transformer;

use Symfony\Component\Templating\TemplateReference;

class TwigTransformer implements TransformerInterface
{
    protected $container;

    /**
     * Constructor
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * getTemplate
     */
    public function getTemplate()
    {
        return $this->container->get('templating');
    }

    /**
     * getExporterManager
     */
    public function getExporterManager()
    {
        return $this->container->get('idci_exporter.manager');
    }

    /**
     * getTemplatePath
     *
     * @param Object $entity
     * @param string $format
     * @return string
     */
    public function getTemplatePath($entity, $format)
    {
        $reflection = $this
            ->getExporterManager()
            ->getEntityReflectionClass($entity)
        ;

        // By default
        $templatePath = sprintf('%s/../Resources/exporter/%s',
            dirname($reflection->getFilename()),
            $reflection->getShortName()
        );

        $transformerOptions = $this
            ->getExporterManager()
            ->getEntityTransformerOptions($entity, $format)
        ;
        if(isset($transformerOptions['template_path'])) {
            $templatePath = $transformerOptions['template_path'];
        }

        return $templatePath;
    }

    /**
     * getTemplateNameFormat
     *
     * @param Object $entity
     * @param string $format
     * @return string
     */
    public function getTemplateNameFormat($entity, $format)
    {
        $reflection = $this
            ->getExporterManager()
            ->getEntityReflectionClass($entity)
        ;

        // By default
        $templateNameFormat = 'export.%s.twig';

        $transformerOptions = $this
            ->getExporterManager()
            ->getEntityTransformerOptions($entity, $format)
        ;

        if(isset($transformerOptions['template_name_format'])) {
            $templateNameFormat = $transformerOptions['template_name_format'];
        }

        return $templateNameFormat;
    }

    /**
     * transform
     *
     * @param Object $entity
     * @param string $format
     * @return string
     */
    public function transform($entity, $format)
    {
        $templatePath = $this->getTemplatePath($entity, $format);
        $this->container->get('twig.loader')->addPath($templatePath);

        $templateNameFormat = $this->getTemplateNameFormat($entity, $format);
        $template = sprintf($templateNameFormat, $format);

        return $this->getTemplate()->render(
            $template,
            array('entity' => $entity)
        );
    }
}
