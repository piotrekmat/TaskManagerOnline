<?php
/**
 * Created by PhpStorm.
 * User: rafalmnich
 * Date: 14.06.2016
 * Time: 12:23
 */

namespace Webservice\Factory;


use Doctrine\ORM\EntityManager;
use Product\Entity\ProductVersion;
use Product\Repository\ProductVersionRepository;
use System\Entity\Channel;
use System\Repository\ChannelRepository;
use Webservice\Services\Idealo;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IdealoFactory implements FactoryInterface
{

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		/** @var EntityManager $entityManager */
		$entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
		/** @var ProductVersionRepository $productVersionRepository */
		$productVersionRepository = $entityManager->getRepository(ProductVersion::class);

		/** @var ChannelRepository $channelRepository */
		$channelRepository = $entityManager->getRepository(Channel::class);

		return new Idealo($entityManager, $productVersionRepository, $channelRepository);
	}
}