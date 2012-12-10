<?php

namespace IDCI\Bundle\ExporterBundle\Transformer;

use Symfony\Component\Templating\TemplateReference;

class TwigTransformer implements TransformerInterface
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getTemplate()
    {
        return $this->container->get('templating');
    }

    public function getKernel()
    {
        return $this->container->get('kernel');
    }

    public function transform($entity, $format)
    {
        $reflection = new \ReflectionClass($entity);
        $templatePath = sprintf('%s/../Resources/exporter/%s',
            dirname($reflection->getFilename()),
            $reflection->getShortName()
        );

        $this->container->get('twig.loader')->addPath($templatePath);
        $template = sprintf('export.%s.twig', $format);

        return $this->getTemplate()->render(
            $template,
            array('entity' => $entity)
        );
    }
}
