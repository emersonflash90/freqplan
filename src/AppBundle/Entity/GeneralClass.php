<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GeneralClass
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class GeneralClass {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    protected $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_date", type="datetime")
     */
    protected $lastUpdateDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="search_data", type="text", nullable=true)
     */
    protected $searchData;

    public function __construct() {
        $this->state = 1;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return GeneralClass
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return GeneralClass
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState() {
        return $this->state;
    }


    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return GeneralClass
     */
    public function setCreateDate($createDate) {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->createDate;
    }

    /**
     * Set lastUpdateDate
     *
     * @param \DateTime $lastUpdateDate
     *
     * @return GeneralClass
     */
    public function setLastUpdateDate($lastUpdateDate) {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    /**
     * Get lastUpdateDate
     *
     * @return \DateTime
     */
    public function getLastUpdateDate() {
        return $this->lastUpdateDate;
    }
    
    /**
     * Set searchData
     */
    public abstract function setSearchData();

    /**
     * Get $searchData
     *
     * @return string
     */
    public function getSearchData() {
        return $this->searchData;
    }

    /**
     * @ORM\PreUpdate() 
     */
    public function preUpdate() {
        $this->lastUpdateDate = new \DateTime('now');
        $this->sendingDate = new \DateTime('now');
        $this->setSearchData();
    }

    /**
     * @ORM\PrePersist() 
     */
    public function prePersist() {
        $this->createDate = new \DateTime('now');
        $this->lastUpdateDate = new \DateTime('now');
        $this->sendingDate = new \DateTime('now');
        $this->status = 1;
        $this->setSearchData();
    }
    
}
