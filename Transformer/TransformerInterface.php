<?php

namespace IDCI\Bundle\ExporterBundle\Transformer;

interface TransformerInterface
{
    public function transform($entity, $format);
}
