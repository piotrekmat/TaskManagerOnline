<?php

namespace Webservice\Entity\Google;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="products.google_categories")
 * @ORM\Entity(repositoryClass="Webservice\Repository\Google\GoogleCategoryRepository")
 */
class GoogleCategory
{
    use \Application\Behavior\MagicAccessTrait;
    use \Application\Behavior\TimestampableTrait;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Webservice\Entity\Google
     *
     * @ORM\ManyToOne(targetEntity="Webservice\Entity\Google\GoogleCategory", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @var \Webservice\Entity\Google
     *
     * @ORM\OneToMany(targetEntity="Webservice\Entity\Google\GoogleCategory", mappedBy="parent")
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Product\Entity\Category", mappedBy="googleCategory")
     */
    private $categories;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $numberGoogleCategory;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $nameGoogleCategory;

    public function getTranslations()
    {
        return $this->translations;
    }

    public function __construct()
    {
        $this->categories       = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
