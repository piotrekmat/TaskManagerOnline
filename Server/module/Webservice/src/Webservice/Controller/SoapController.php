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


        $soap = new Server();
        $request = $this->getRequest();
        $response = $this->getResponse();

        $controller = $this->params('soapcontroller');
        $action = $this->params('soapaction');
        $classWsdl = sprintf("%s\\Webservice\\%s", ucfirst($controller), ucfirst($action));

        if (isset($_GET['wsdl'])) {
            $uri = $this->getUri(true);
            $wsdlGenerator = new AutoDiscover(new Strategy());
            $wsdlGenerator->setServiceName($classWsdl);
            $wsdlGenerator->setClass($classWsdl);
            $wsdlGenerator->setWsdlClass($classWsdl);
            $wsdlGenerator->setUri($uri);
            $wsdl = $wsdlGenerator->generate();
            var_dump($wsdl);
            die;
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/wsdl+xml');
            $response->setContent($wsdl->toXml());
        } else {
            $soap->setReturnResponse(true);
            $uri = $this->getUri(true);
            $wsdl = new $classWsdl($uri);
            $soap->setUri($uri);
            $soap->setClass($wsdl);
            $soapResponse = $soap->handle($request);
            if ($soapResponse instanceof SoapFault) {
                $soapResponse = (string)$soapResponse;
            }

            $response->getHeaders()->addHeaderLine('Content-Type', 'application/xml');
            $response->setContent($soapResponse);
        }


        switch ($request->getMethod()) {
            case 'GET':

                break;

            case 'POST':

                break;

            default:
                $response->setStatusCode(405);
                $response->getHeaders()->addHeaderLine('Allow', 'GET,POST');
                break;
        }
//        var_dump($response);
        return $response;
    }

    private function getUri($wsdl = null)
    {
        if (empty($this->uri)) {
            $this->uri = $this->getRequest()->getUri();
            if ($wsdl) {
                $this->uri = $this->uri . "?wsdl";
            }
        }
        return $this->uri;
    }
}
