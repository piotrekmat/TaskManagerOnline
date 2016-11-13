<?php
namespace Webservice;

class Module
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getConfig()
	{
		$config = [];
		$configFiles = [
			include __DIR__ . '/config/module.config.php',
			include __DIR__ . '/config/custom.config.php',
		];
		foreach ($configFiles as $file) {
			$config = \Zend\Stdlib\ArrayUtils::merge($config, $file);
		}
		return $config;
	}

	public function getServiceConfig()
	{
		return [
			'factories' => [
				'Webservice\Services\Idealo' => 'Webservice\Factory\IdealoFactory',
			],
		];
	}

}
