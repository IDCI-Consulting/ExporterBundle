<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Exceptions;

class UndefinedExportableEntityException extends \Exception
{
    /**
     * The constructor.
     */
    public function __construct($entity)
    {
        $reflection = new \ReflectionClass($entity);

        parent::__construct(sprintf(
            'The entity %s is not defined as an exportable entity',
            $reflection->getName()
        ));
    }
}
