<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\ExporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/query", name="exporter_api_norewrite", defaults={"entity_reference"="null", "_format"="xml"})
     * @Route("/{entity_reference}.{_format}", name="exporter_api", defaults={"_format"="xml"})
     */
    public function indexAction(Request $request, $entity_reference)
    {
        $format = $request->getRequestFormat();
        if($request->query->has('format')) {
            $format = $request->query->get('format');
        }

        if($entity_reference == "null") {
            $entity_reference = $request->query->get('entity_reference');
        }

        $entities = $this->get('idci_exporter.manager')->extract(
            $entity_reference,
            $request->query->all()
        );

        $export = $this->get('idci_exporter.manager')->export(
            $entities,
            $format,
            $request->query->all()
        );

        $response = new Response();
        $response->setContent($export->getContent());
        $response->headers->set('Content-Type', $export->getContentType());

        return $response;
    }
}
