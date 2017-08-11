<?php

namespace ES\AgendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="ES\AgendaBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBeginning", type="datetime")
     */
    private $dateBeginning;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnding", type="datetime")
     */
    private $dateEnding;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255)
     */
    private $place;

    public function __construct()
    {
        $this->dateBeginning = new \Datetime();
        $this->dateEnding = $this->dateBeginning;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateBeginning
     *
     * @param \DateTime $dateBeginning
     *
     * @return Event
     */
    public function setDateBeginning($dateBeginning)
    {
        $this->dateBeginning = $dateBeginning;

        return $this;
    }

    /**
     * Get dateBeginning
     *
     * @return \DateTime
     */
    public function getDateBeginning()
    {
        return $this->dateBeginning;
    }

    /**
     * Set dateEnding
     *
     * @param \DateTime $dateEnding
     *
     * @return Event
     */
    public function setDateEnding($dateEnding)
    {
        $this->dateEnding = $dateEnding;

        return $this;
    }

    /**
     * Get dateEnding
     *
     * @return \DateTime
     */
    public function getDateEnding()
    {
        return $this->dateEnding;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Event
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }
}

