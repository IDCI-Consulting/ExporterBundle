ExporterBundle
==============

Symfony2 exporter bundle


Instalation
===========

To install this bundle please follow the next steps:

First add the dependency to your `composer.json` file:

    "require": {
        ...
        "idci/exporter-bundle": "dev-master"
    },

Then install the bundle with the command:

    php composer update

Enable the bundle in your application kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new IDCI\Bundle\ExporterBundle\IDCIExporterBundle(),
        );
    }

Now the Bundle is installed.


How to use
==========

The default export is based on twig templates. So you have to create a directory
in `Resources/exporter/EntityName`. Then create needed templates which convert
your entity into a specific format (csv, ics, json, xml for the moment) like this

  * export.csv.twig
  * export.ics.twig
  * export.json.twig
  * export.xml.twig

Finaly simply use the service as follow to export your entities into the given format:

    $this->container->get('idci_exporter.manager')->export($entities, $format);
