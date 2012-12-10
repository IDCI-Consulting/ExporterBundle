ExporterBundle
==============

Symfony2 exporter bundle


Instalation
===========

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:

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

    $export = $this->container->get('idci_exporter.manager')->export($entities, $format);

This will return an `Export` object which contain the exported data `getContent()`
and the used mime type format `getContentType()`.

Advanced configuration
======================

You can customize entities transformers for each format.
By default, 'idci_exporter.transformer_twig' service is used to transform you entity.
To use an other tranformer, create your own service which implements `TransformerInterface.php`
Then declare it for a given format like this:

    idci_exporter:
        entities:
            "My\Entity\Namespaced\Name":
                formats:
                    csv:
                        transformer: "myTransformerServiceName"

If you want to change the TwigTransformer template path or the template name:

    idci_exporter:
        entities:
            "My\Entity\Namespaced\Name":
                formats:
                    xml:
                        template_path: "my/new/template/path"
                        template_name_format: "myFormat.%s.ext"

By default template path is looking in the entity bundle dir `Resources/exporter/EntityName/`.
And the template name format looks like `export.%s.twig` with %s replaced by the given format.


Todo
====

 * Simplify the override of buildHeader() and buildFooter() funcion on an Export object.
