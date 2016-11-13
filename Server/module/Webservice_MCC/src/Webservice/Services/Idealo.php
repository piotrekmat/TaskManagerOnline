<?php
/**
 * Created by PhpStorm.
 * User: rafalmnich
 * Date: 14.06.2016
 * Time: 11:50
 */

namespace Webservice\Services;


use Application\Service\BaseService;
use Doctrine\ORM\EntityManager;
use Product\Entity\ProductVersion;
use Product\Repository\ProductVersionRepository;
use System\Entity\Channel;
use System\Repository\ChannelRepository;

class Idealo extends BaseService
{
	/** @var ProductVersionRepository $productVersionRepository */
	private $productVersionRepository;
	/** @var Channel $idealoChannel */
	private $idealoChannel;
	private $channelRepository;

	public function __construct(EntityManager $entityManager, ProductVersionRepository $productVersionRepository, ChannelRepository $channelRepository)
	{
		$this->entityManager            = $entityManager;
		$this->productVersionRepository = $productVersionRepository;
		$this->channelRepository        = $channelRepository;
		$this->idealoChannel            = $this->getIdealoChannel();
	}

	public function getProductsJson()
	{
		$productVersions      = $this->productVersionRepository->getActiveProductVersions($this->idealoChannel->getId());
		$productVersionsArray = [];

		/** @var ProductVersion $productVersion */
		foreach ($productVersions as $productVersion) {
			if (empty($productVersion->getEans()->current()->getEan())) {
				continue;
			}
			$productVersionsArray[] = [
				'id'   => $productVersion->getId(),
				'ean'  => $productVersion->getEans()->current()->getEan(),
				'freq' => 'hour',
			];
		}

		return $productVersionsArray;
	}

	private function getIdealoChannel()
	{
		$idealoChannels = $this->channelRepository->getChannelsByType('lapado_idealo');
		if (!empty($idealoChannels)) {
			return current($idealoChannels);
		}

		return null;
	}
}