<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportEntityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('exporter:export:entities')
            ->setDescription('Export entities in a given format into a specific file')
            ->addOption(
                'entity',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity namespace to export'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'The export format to use',
                'xml'
            )
            ->addOption(
                'to',
                null,
                InputOption::VALUE_REQUIRED,
                'The export destination file'
            )
            ->addOption(
                'query',
                null,
                InputOption::VALUE_OPTIONAL,
                'To export specifics entities which match the given query'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getOption('entity');
        $format = $input->getOption('format');
        $to = $input->getOption('to');
        $query = $input->getOption('query');

        var_dump($entity, $format, $to, $query);
    }
}
