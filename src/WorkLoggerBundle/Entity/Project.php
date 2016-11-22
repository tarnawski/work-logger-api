<?php

namespace WorkLoggerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Project
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var  ArrayCollection|Record[] */
    private $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
            $record->setProject($this);
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
