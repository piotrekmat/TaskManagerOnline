<?php

namespace Webservice\Controller;

use \Application\Controller\AbstractController;
use Zend\Mvc\Application;
use \Zend\Soap\AutoDiscover;
use \Zend\Soap\Server;
use \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence as Strategy;

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

        if (isset($_GET['wsdl'])) {
            $uri = $this->getUri(true);
            $wsdlGenerator = new AutoDiscover(new Strategy());
//            $wsdlGenerator->setServiceName($classWsdl);
            $wsdlGenerator->setBindingStyle(['style' => 'document']);
            $wsdlGenerator->setOperationBodyStyle(['use' => 'literal']);
            $wsdlGenerator->setUri($uri);
            $wsdlGenerator->setClass($classWsdl);
            $wsdl = $wsdlGenerator->generate();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/xml');
            $response->setContent($wsdl->toXml());
        } else {
            $soap = new Server();
            $soap->setReturnResponse(true);
            $uri = $this->getUri(true);
            $soap->setUri($uri);
            $soap->setClass($classWsdl);
            $soapResponse = $soap->handle();
            if ($soapResponse instanceof SoapFault) {
                $soapResponse = (string)$soapResponse;
            }
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/xml');
            $response->setContent($soapResponse);
        }
        return $response;
    }

    private function getUri($wsdl = null)
    {
        if (empty($this->uri)) {
            $this->uri = $this->getRequest()->getUri();
            if (true === $wsdl) {
                $this->uri = $this->uri . "";
            }
        }
        return $this->uri;
    }
}
