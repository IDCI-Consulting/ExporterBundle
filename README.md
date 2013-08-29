ExporterBundle
==============

Symfony2 exporter bundle


Installation
===========

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:

```json
"require": {
    ...
    "idci/exporter-bundle": "1.0"
},
```

Then install the bundle with the command:

```sh
php composer update
```

Enable the bundle in your application kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new IDCI\Bundle\ExporterBundle\IDCIExporterBundle(),
    );
}
```

Now the Bundle is installed.


How to use
==========

First declare which entities can be exported in your `config.yml`:

```yml
idci_exporter:
    entities:
       entity_reference:
            class: "My\\Entity\\Namespaced\\Name"
```

By default, the export use twig engine to create exported files. We will see later
that you can create your own transformer. For the moment create a directory
in `YourBundle/Resources/exporter/EntityName`. Then create the needed templates which convert
your entity into a specific format (csv, ics, json, xml for the moment) like this

  * export.csv.twig
  * export.ics.twig
  * export.json.twig
  * export.xml.twig

Within your template you should usually use the twig variable `entity` such as `{{ entity.id }}`.

Finaly simply use the service as follow to export your entities into the given format:

```php
$export = $this->container->get('idci_exporter.manager')->export($entities, $format);
```

This will return an `Export` object which contain the exported data.

```php
$export->getContent();
```

To get the mime type format

```php
$export->getContentType();
```

Advanced configuration
======================

You can customize entities transformers for each format. By default, `idci_exporter.transformer_twig`
service is used to transform you entity. To use an other tranformer, create your
own service which implements `TransformerInterface.php` and then associated it 
with a specific format like this:
```yml
idci_exporter:
    entities:
       entity_reference:
            class: "My\\Entity\\Namespaced\\Name"
            formats:
                json:
                    transformer:
                        service: "myTransformerServiceName"
```

If you want to change the TwigTransformer template path or the template name, 
you can use specified some transformer options:

```yml
idci_exporter:
    entities:
       entity_reference:
            class: "My\\Entity\\Namespaced\\Name"
            formats:
                json:
                    transformer:
                        service: "myTransformerServiceName"
                        options:
                            template_path: "my/new/template/path"
                            template_name_format: "myFormat.%s.ext"
```

By default template path is looking in the entity bundle dir `Resources/exporter/EntityName/`.
And the template name format looks like `export.%s.twig` with %s replaced by the format (ex: xml, json, csv, ...).


How to export in jsonp format
=============================

If you already have a json export, it will be very easy to export into jsonp format.
Simply add a new format and set the template_name options to `export.json.twig`:

So just add theses in your `config.yml`:

```yml
idci_exporter:
    entities:
        entity_reference:
            class: "My\\Entity\\Namespaced\\Name"
            formats:
                jsonp:
                    transformer:
                        options:
                            template_name_format: "export.json.twig"
```

Use the API
===========

This bundle help you to get your entities in a given format via HTTP requests.
To do that, add its controller in the `app/config/routing.yml` like this:

```yml
idci_exporter:
    resource: "../../vendor/idci/exporter-bundle/IDCI/Bundle/ExporterBundle/Controller"
    type:     annotation
```

Then you will be able to send request to the following routes:

    exporter_api_norewrite            ANY    /api/query
    exporter_api                      ANY    /api/{entity_reference}.{_format}

`{entity_reference}` is a required parameter. This is the value which is define in
your `config.yml`:

```yml
idci_exporter:
    entities:
        entity_reference:
            class: "My\\Entity\\Namespaced\\Name"
```

If you get a `UndefinedExportableEntityException` this mean that you don't have
well defined the export config for your Entity.

Then you have to create an `extract` function in your EntityRepository which return
a `DoctrineCollection`. This function get `$params` variable as arguments which
can be used to filters your results.

Here is an example:

```php
/**
 * extractQueryBuilder
 *
 * @param array $params
 * @return QueryBuilder
 */
public function extractQueryBuilder($params)
{
    $qb = $this->createQueryBuilder('cer');

    if(isset($params['id'])) {
        $qb
            ->andWhere('cer.id = :id')
            ->setParameter('id', $params['id'])
        ;
    }

    if(isset($params['category_id'])) {
        $qb
            ->leftJoin('cer.categories', 'c')
            ->andWhere('c.id = :cat_id')
            ->setParameter('cat_id', $params['category_id'])
        ;
    }

    if(isset($params['category_ids'])) {
        $qb
            ->leftJoin('cer.categories', 'cs')
            ->andWhere($qb->expr()->in('cs.id', $params['category_ids']))
        ;
    }

    return $qb;
}

/**
 * extractQuery
 *
 * @param array $params
 * @return Query
 */
public function extractQuery($params)
{
    $qb = $this->extractQueryBuilder($params);

    return is_null($qb) ? $qb : $qb->getQuery();
}

/**
 * extract
 *
 * @param array $params
 * @return DoctrineCollection
 */
public function extract($params)
{
    $q = $this->extractQuery($params);

    return is_null($q) ? array() : $q->getResult();
}
```

Now you can query your entities like this:

    http://mydomaine/api/query?entity_reference=my_entity_reference&format=xml

or

    http://mydomaine/api/my_entity_reference.xml
