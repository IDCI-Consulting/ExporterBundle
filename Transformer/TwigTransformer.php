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
     * getTemplatePath
     *
     * @param Object $entity
     * @param string $format
     * @return string
     */
    public function getTemplatePath($entity, $format)
    {
        $reflection = new \ReflectionClass($entity);
        if(self::isProxyClass($reflection) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }
        // By default
        $templatePath = sprintf('%s/../Resources/exporter/%s',
            dirname($reflection->getFilename()),
            $reflection->getShortName()
        );

        $configuration = $this->container->getParameter('entitiesConfiguration');
        if(isset($configuration[$reflection->getName()]['formats'][$format]['template_path'])) {
            $templatePath = $configuration[$reflection->getName()]['formats'][$format]['template_path'];
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
        $reflection = new \ReflectionClass($entity);
        if(self::isProxyClass($reflection) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }
        // By default
        $templateNameFormat = 'export.%s.twig';

        $configuration = $this->container->getParameter('entitiesConfiguration');
        if(isset($configuration[$reflection->getName()]['formats'][$format]['template_name_format'])) {
            $templateNameFormat = $configuration[$reflection->getName()]['formats'][$format]['template_name_format'];
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
