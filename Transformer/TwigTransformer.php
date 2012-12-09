<?php

namespace IDCI\Bundle\ExporterBundle\Transformer;

class TwigTransformer implements TransformerInterface
{
    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function transform($entity, $format)
    {
        die('twig transform');
    }
}
