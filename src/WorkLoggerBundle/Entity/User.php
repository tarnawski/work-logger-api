<?php

namespace WorkLoggerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class User extends BaseUser
{
    const ROLE_API = 'ROLE_API';

    public static $ROLES = array(
        self::ROLE_API => self::ROLE_API
    );

    /** @var integer */
    protected $id;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $phone;

    /** @var string */
    private $department;

    /** @var  ArrayCollection|Record[] */
    private $records;

    public function __construct()
    {
        parent::__construct();
        $this->records = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param string $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record
     * @return Record
     */
    public function addRecord(Record $record)
    {
        if (!$this->records->contains($record)) {
            $record->setUser($this);
            $this->records[] = $record;
        }
        return $this;
    }

    /**
     * @param Record
     */
    public function removeRecord(Record $record)
    {
        $this->records->removeElement($record);
    }
}
