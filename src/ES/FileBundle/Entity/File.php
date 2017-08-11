<?php

namespace ES\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="ES\FileBundle\Repository\FileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class File
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="ES\FileBundle\Entity\Directory",
     inversedBy="content_files")
     */
    private $directory;

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
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    private $file;

    private $tempFilename;



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
     * @return File
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
     * Set name
     *
     * @param string $extension
     *
     * @return File
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        if (null !== $this->extension) {
            $this->tempFilename = $this->extension;
            $this->extension = null;
        }
    }

    /**
     * @ORM\PrePersist()
     *
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file)
        {
            return;
        }

        $this->extension = $this->file->guessExtension();
        $this->name = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    /**
     * @ORM\PostPersist()
     *
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file)
        {
            return;
        }

        if (null !== $this->tempFilename) 
        {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) 
            {
                unlink($oldFile);
            }
        }

        $this->file->move($this->getUploadRootDir(), $this->id.'.'.$this->extension);
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFilename)) 
        {
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        return 'uploads/file';
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getExtension();
    }

    protected function getUploadRootDir()

    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Set directory
     *
     * @param \ES\FileBundle\Entity\Directory $directory
     *
     * @return File
     */
    public function setDirectory(\ES\FileBundle\Entity\Directory $directory = null)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return \ES\FileBundle\Entity\Directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
