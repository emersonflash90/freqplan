<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equipment
 *
 * @ORM\Table(name="equipment")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\EquipmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Equipment extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="equipment_name", type="string", length=255)
     */
    private $equipmentName;
    
    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float", precision=10, scale=0, nullable=true)
     */
    private $cost;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="duree_vie", type="integer", nullable=true)
     */
    private $dureeVie;
    
    /**
     * @var \Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site", referencedColumnName="id")
     * })
     */
    private $site;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Set equipmentName
     *
     * @param string $equipmentName
     *
     * @return Equipment
     */
    public function setEquipmentName($equipmentName) {
        $this->equipmentName = $equipmentName;

        return $this;
    }

    /**
     * Get equipmentName
     *
     * @return string
     */
    public function getEquipmentName() {
        return $this->equipmentName;
    }
    
    /**
     * Set cost
     *
     * @param float $cost
     *
     * @return Equipment
     */
    public function setCost($cost) {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost() {
        return $this->cost;
    }
    
    /**
     * Set dureeVie
     *
     * @param integer $dureeVie
     *
     * @return Equipment
     */
    public function setDureeVie($dureeVie) {
        $this->dureeVie = $dureeVie;

        return $this;
    }

    /**
     * Get dureeVie
     *
     * @return integer
     */
    public function getDureeVie() {
        return $this->dureeVie;
    }
    
    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return Equipment
     */
    public function setSite(\AppBundle\Entity\Site $site=null) {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite() {
        return $this->site;
    }
    
    
    public function setSearchData() {
        $this->searchData = $this->getEquipmentName()." ".$this->getCost()." ".$this->getDureeVie();
    }
}
