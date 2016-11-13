<?php

namespace Webservice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="modules.webservice_hashes", uniqueConstraints={
 *	@ORM\UniqueConstraint(name="typeHash", columns={"type", "hash"})
 * }, indexes={@ORM\Index(name="searchHash", columns={"hash"})})
 * @ORM\Entity(repositoryClass="Application\Repository\MccEntityRepository")
 */
class Hash
{
	use \Application\Behavior\MagicAccessTrait;
	
	/**
	 * @var string
	 * 
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $type;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $hash;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $hashValue;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $requestIp;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $requestDate;

	public function generateHash() {
		return md5('8ufR$kut'. uniqid(rand() . 'FaS4e*uc', true));
	}
}
	
