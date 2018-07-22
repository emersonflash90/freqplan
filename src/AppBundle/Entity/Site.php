<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\SiteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Site extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="tnumber", type="string", length=255)
     */
    private $tNumber;
    
    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="text", nullable=true)
     */
    private $siteName;
    
    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitude;
    
    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitude;
    
    /**
     * @var string
     *
     * @ORM\Column(name="site_city", type="string", length=255, nullable=true)
     */
    private $siteCity;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="site_type", type="integer", nullable=true)
     */
    private $siteType;
    
    /**
     * @var float
     *
     * @ORM\Column(name="azimut", type="float", precision=10, scale=0, nullable=true)
     */
    private $azimut;
    
    /**
     * @var float
     *
     * @ORM\Column(name="frequence", type="float", precision=10, scale=0, nullable=true)
     */
    private $frequence;
    
    /**
     * @var string
     *
     * @ORM\Column(name="polarisation", type="string", length=255, nullable=true)
     */
    private $polarisation;
    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Site", mappedBy="site", cascade={"remove", "persist"})
     */
    private $nodalSites;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Equipment", mappedBy="site", cascade={"remove", "persist"})
     */
    private $equipments;
    
    

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->nodalSites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->equipments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set tNumber
     *
     * @param string $tNumber
     *
     * @return Site
     */
    public function setTNumber($tNumber) {
        $this->tNumber = $tNumber;

        return $this;
    }

    /**
     * Get tNumber
     *
     * @return string
     */
    public function getTNumber() {
        return $this->tNumber;
    }
    
    /**
     * Set siteName
     *
     * @param string $siteName
     *
     * @return Site
     */
    public function setSiteName($siteName) {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string
     */
    public function getSiteName() {
        return $this->siteName;
    }
 
    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Site
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }
    
    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Site
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }
    
    /**
     * Set siteCity
     *
     * @param string $siteCity
     *
     * @return Site
     */
    public function setSiteCity($siteCity) {
        $this->siteCity = $siteCity;

        return $this;
    }

    /**
     * Get siteCity
     *
     * @return string
     */
    public function getSiteCity() {
        return $this->siteCity;
    }
    
    /**
     * Set azimut
     *
     * @param float $azimut
     *
     * @return Site
     */
    public function setAzimut($azimut) {
        $this->azimut = $azimut;

        return $this;
    }

    /**
     * Get azimut
     *
     * @return float
     */
    public function getAzimut() {
        return $this->azimut;
    }
    
    /**
     * Set frequence
     *
     * @param float $frequence
     *
     * @return Site
     */
    public function setFrequence($frequence) {
        $this->frequence = $frequence;

        return $this;
    }

    /**
     * Get frequence
     *
     * @return float
     */
    public function getFrequence() {
        return $this->frequence;
    }
    
    /**
     * Set siteType
     *
     * @param integer $siteType
     *
     * @return Site
     */
    public function setSiteType($siteType) {
        $this->siteType = $siteType;

        return $this;
    }

    /**
     * Get siteType
     *
     * @return integer
     */
    public function getSiteType() {
        return $this->siteType;
    }
    
    /**
     * Set polarisation
     *
     * @param string polarisation
     *
     * @return Site
     */
    public function setPolarisation($polarisation) {
        $this->polarisation = $polarisation;

        return $this;
    }

    /**
     * Get polarisation
     *
     * @return string
     */
    public function getPolarisation() {
        return $this->polarisation;
    }
    
    
    
    /**
     * Add nodalSite
     *
     * @param \AppBundle\Entity\Site $nodalSite 
     * @return Site
     */
    public function addNodalSite(\AppBundle\Entity\Site $nodalSite) {
        $this->nodalSites[] = $nodalSite;
        return $this;
    }

    /**
     * Get nodalSites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNodalSites() {
        return $this->nodalSites;
    }

    /**
     * Set nodalSites
     *
     * @param \Doctrine\Common\Collections\Collection $nodalSites
     * @return Site
     */
    public function setNodalSites(\Doctrine\Common\Collections\Collection $nodalSites = null) {
        $this->nodalSites = $nodalSites;

        return $this;
    }

    /**
     * Remove nodalSite
     *
     * @param \AppBundle\Entity\NodalSite $nodalSite
     * @return Site
     */
    public function removeNodalSite(\AppBundle\Entity\NodalSite $nodalSite) {
        $this->nodalSites->removeElement($nodalSite);
        return $this;
    }
    
    /**
     * Add equipment
     *
     * @param \AppBundle\Entity\Equipment $equipment 
     * @return Site
     */
    public function addEquipment(\AppBundle\Entity\Equipment $equipment) {
        $this->equipments[] = $equipment;
        return $this;
    }

    /**
     * Get equipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipments() {
        return $this->equipments;
    }

    /**
     * Set equipments
     *
     * @param \Doctrine\Common\Collections\Collection $equipments
     * @return Site
     */
    public function setEquipments(\Doctrine\Common\Collections\Collection $equipments = null) {
        $this->equipments = $equipments;

        return $this;
    }

    /**
     * Remove equipment
     *
     * @param \AppBundle\Entity\Equipment $equipment
     * @return Site
     */
    public function removeEquipment(\AppBundle\Entity\Equipment $equipment) {
        $this->equipments->removeElement($equipment);
        return $this;
    }
    
    public function setSearchData() {
        $this->searchData = $this->getTNumber()." ".$this->getSiteName()." ".$this->getLatitude()." ".$this->getLongitude()." ".$this->getSiteCity()." ".$this->getAzimut()." ".$this->getFrequence()." ".$this->getPolarisation();
    }
}
