<?php

namespace ES\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Advert
 *
 * @ORM\Table(name="advert")
 * @ORM\Entity(repositoryClass="ES\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 */
class Advert
{
    /**
     *
     * @ORM\OneToOne(targetEntity="ES\FileBundle\Entity\File", cascade={"persist", "remove"})
     *
     *@Assert\Valid()
     */
    private $budget;

    /**
     *
     * @ORM\ManyToMany(targetEntity="ES\PlatformBundle\Entity\Category", cascade={"persist"})
     *
     */
    private $categories;

    /**
     *
     * @ORM\ManyToMany(targetEntity="ES\UserBundle\Entity\User")
     *
     */
    private $participants;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="nbperson", type="integer")
     */
    private $nbperson;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     *
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     *
     * @Assert\DateTime()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     *
     * @Assert\DateTime()
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\Length(min=10)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="ES\UserBundle\Entity\User", cascade={"persist"})
     *
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank()
     */
    private $content;

    /**
    * @var boolean
    *
    * @ORM\Column(name="descriptionAccepted", type="boolean")
    */
    private $descriptionAccepted = false;

    /**
    * @var boolean
    *
    * @ORM\Column(name="budgetAccepted", type="boolean")
    */
    private $budgetAccepted = false;

    /**
    * @var boolean
    *
    * @ORM\Column(name="projectAccepted", type="boolean")
    */
    private $projectAccepted = false;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Gedmo\Slug(fields={"title"})
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     **/
    private $slug;

    public function __construct()
    {
        $this->date = new \Datetime();
        $this->startDate = new \Datetime();
        $this->endDate = new \Datetime();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add category
     *
     * @param \ES\PlatformBundle\Entity\Category $category
     *
     * @return Advert
     */
    public function addCategory(\ES\PlatformBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \ES\PlatformBundle\Entity\Category $category
     */
    public function removeCategory(\ES\PlatformBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set author
     *
     * @param \ES\UserBundle\Entity\User $author
     *
     * @return Advert
     */
    public function setAuthor(\ES\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \ES\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set descriptionAccepted
     *
     * @param boolean $descriptionAccepted
     *
     * @return Advert
     */
    public function setDescriptionAccepted($descriptionAccepted)
    {
        $this->descriptionAccepted = $descriptionAccepted;

        return $this;
    }

    /**
     * Get descriptionAccepted
     *
     * @return boolean
     */
    public function getDescriptionAccepted()
    {
        return $this->descriptionAccepted;
    }

    /**
     * Set budgetAccepted
     *
     * @param boolean $budgetAccepted
     *
     * @return Advert
     */
    public function setBudgetAccepted($budgetAccepted)
    {
        $this->budgetAccepted = $budgetAccepted;

        return $this;
    }

    /**
     * Get budgetAccepted
     *
     * @return boolean
     */
    public function getBudgetAccepted()
    {
        return $this->budgetAccepted;
    }

    /**
     * Set projectAccepted
     *
     * @param boolean $projectAccepted
     *
     * @return Advert
     */
    public function setProjectAccepted($projectAccepted)
    {
        $this->projectAccepted = $projectAccepted;

        return $this;
    }

    /**
     * Get projectAccepted
     *
     * @return boolean
     */
    public function getProjectAccepted()
    {
        return $this->projectAccepted;
    }

    /**
     * Set budget
     *
     * @param \ES\FileBundle\Entity\File $budget
     *
     * @return Advert
     */
    public function setBudget(\ES\FileBundle\Entity\File $budget = null)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return \ES\FileBundle\Entity\File
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Advert
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Advert
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Advert
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Advert
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

    /**
     * Set nbperson
     *
     * @param integer $nbperson
     *
     * @return Advert
     */
    public function setNbperson($nbperson)
    {
        $this->nbperson = $nbperson;

        return $this;
    }

    /**
     * Get nbperson
     *
     * @return integer
     */
    public function getNbperson()
    {
        return $this->nbperson;
    }

    /**
     * Add participant
     *
     * @param \ES\UserBundle\Entity\User $participant
     *
     * @return Advert
     */
    public function addParticipant(\ES\UserBundle\Entity\User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \ES\UserBundle\Entity\User $participant
     */
    public function removeParticipant(\ES\UserBundle\Entity\User $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }
}
