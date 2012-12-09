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

