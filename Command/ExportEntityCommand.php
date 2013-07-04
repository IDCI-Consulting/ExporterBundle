<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: GPL
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
                'query',
                null,
                InputOption::VALUE_REQUIRED,
                'The query used to extract entities (findAll by default)'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'The export format to use'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The exported file name'
            )
            ->addOption(
                'to',
                null,
                InputOption::VALUE_REQUIRED,
                'The exported file destination'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$entity = $input->getOption('entity')) {
            $entity = $dialog->ask(
                $output,
                'Please enter the namespace of the entity to export : '
            );
        }
        if (!$query = $input->getOption('query')) {
            $query = $dialog->ask(
                $output,
                'Please enter the query used to extract entities (findAll by default) : ',
                'findAll'
            );
        }
        if (!$format = $input->getOption('format')) {
            $format = $dialog->ask(
                $output,
                'Please enter the format (csv by default) : ',
                'csv'
            );
        }
        if (!$name = $input->getOption('name')) {
            $name = $dialog->ask(
                $output,
                'Please enter the exported file name (export by default) : ',
                'export'
            );
        }
        if (!$to = $input->getOption('to')) {
            $to = $dialog->ask(
                $output,
                'Please enter the exported file destination (web/exports by default) : ',
                'web/exports'
            );
        }

        $em = $this->getContainer()->get("doctrine.orm.entity_manager");
        $entities = $em->getRepository($entity)->$query();
        $export = $this->getContainer()->get('idci_exporter.manager')->export(
            $entities,
            $format
        );

        if($handle = fopen(sprintf("%s/%s.%s", $to, $name, $format), 'w')) {
            fwrite($handle, $export->getContent());
            $output->writeln(sprintf('<info>The %s.%s file was successfully created under %s directory.</info>', $name, $format, $to));
        } else {
            $output->writeln(sprintf('<error>Error: unable to create %s.%s file under %s directory.</error>', $name, $format, $to));
        }
    }
}
