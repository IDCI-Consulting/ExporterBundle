<?php

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
     * getTemplatePath
     *
     * @param Object $entity
     * @param string $format
     * @return string
     */
    public function getTemplatePath($entity, $format)
    {
        $reflection = new \ReflectionClass($entity);
        // By default
        $templatePath = sprintf('%s/../Resources/exporter/%s',
            dirname($reflection->getFilename()),
            $reflection->getShortName()
        );

        $configuration = $this->container->getParameter('entitiesConfiguration');
        if(isset($configuration[get_class($entity)]['formats'][$format]['template_path'])) {
            $templatePath = $configuration[get_class($entity)]['formats'][$format]['template_path'];
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
        // By default
        $templateNameFormat = 'export.%s.twig';

        $configuration = $this->container->getParameter('entitiesConfiguration');
        if(isset($configuration[get_class($entity)]['formats'][$format]['template_name_format'])) {
            $templatePath = $configuration[get_class($entity)]['formats'][$format]['template_name_format'];
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
