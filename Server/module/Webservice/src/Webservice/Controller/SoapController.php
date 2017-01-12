<?php

namespace Webservice\Controller;

use \Application\Controller\AbstractController;
use Zend\Mvc\Application;
use \Zend\Soap\AutoDiscover;
use \Zend\Soap\Server;
use \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence as Strategy;
use \Zend\Soap\Server\DocumentLiteralWrapper;

ini_set("soap.wsdl_cache_enabled", "0");

class SoapController extends AbstractController
{
    protected $wsdlGenerator;
    protected $soap;

    private $uri;

    public function indexAction()
    {
        $response = $this->getResponse();

        $controller = $this->params('soapcontroller');
        $action = $this->params('soapaction');
        $classWsdl = sprintf("%s\\Webservice\\%s", ucfirst($controller), ucfirst($action));
        $serviceName = sprintf("%s%s", ucfirst($controller), ucfirst($action));

        try {
            if (isset($_GET['wsdl'])) {
                $strategy = new Strategy();
                $uri = $this->getUri();
                $wsdlGenerator = new AutoDiscover($strategy);
//                $wsdlGenerator->setServiceName($action);
                $wsdlGenerator->setBindingStyle([
                    'style' => 'document',
                    'transport' => 'http://schemas.xmlsoap.org/soap/http',
                ]);
                $wsdlGenerator->setOperationBodyStyle([
                    'use' => 'literal',
                    'namespace' => $uri,
                ]);

                $wsdlGenerator->setUri($uri);
//                $object = new $classWsdl();
                $wsdlGenerator->setClass($classWsdl);
                $wsdl = $wsdlGenerator->generate();
                $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml');
                $response->setContent($wsdl->toXml());
            } else {
                $uri = $this->getRequest()->getUri();
                $soap = new Server($this->getUri(true));
//                $soap->setSoapVersion(SOAP_1_2);
//                $soap->setReturnResponse(false);
                $dlwcsl = new DocumentLiteralWrapper(new $classWsdl());
//                $dlwcsl = new $classWsdl();
                $soap->setObject($dlwcsl);
                $soap->setClass($classWsdl);
                $soapResponse = $soap->handle();
//                if ($soapResponse instanceof SoapFault) {
//                    $soapResponse = (string)$soapResponse;
//                }
//                $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml');
                $response->setContent($soapResponse);
            }
        } catch (\Exception $ex) {
            print_r($ex->getTraceAsString());
            print_r($ex->getMessage());
        }
        return $response;
    }

    private function getUri($wsdl = null)
    {
        $request = $this->getRequest()->getUri();
        $this->uri = sprintf(
            "%s://%s%s",
            $request->getScheme(),
            $request->getHost(),
            $request->getPath()
        );
        if (true === $wsdl) {
            $this->uri = $this->uri . "?wsdl";
        }

        return $this->uri;
    }
}
