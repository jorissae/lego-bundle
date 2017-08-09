<?php

namespace Idk\LegoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Site
 *
 * @ORM\Table(name="attachable_folder")
 * @ORM\Entity(repositoryClass="Lle\AdminListBundle\Entity\AttachableFolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AttachableFolder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

     /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     */
    private $class;

     /**
     * @var string
     *
     * @ORM\Column(name="real_class", type="string", length=255, nullable=true)
     */
    private $realClass;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @ORM\OneToMany(targetEntity="AttachableFile", mappedBy="folder",cascade={"persist"})
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="AttachableFolder", mappedBy="folder",cascade={"persist"})
     */
    private $folders;

    /**
     * @ORM\ManyToOne(targetEntity="AttachableFolder", inversedBy="folders")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $folder;

    /**
     * @var integer
     *
     * @ORM\Column(name="zone_code", type="string", nullable=true)
     */
    private $zoneCode;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return IdkFinderFolder
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return IdkFinderFolder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add files
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFile $files
     * @return IdkFinderFolder
     */
    public function addFile(AttachableFile $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFile $files
     */
    public function removeFile(AttachableFile $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add folders
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFolder $folders
     * @return IdkFinderFolder
     */
    public function addFolder(AttachableFolder $folders)
    {
        $this->folders[] = $folders;

        return $this;
    }

    /**
     * Remove folders
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFolder $folders
     */
    public function removeFolder(AttachableFolder $folders)
    {
        $this->folders->removeElement($folders);
    }

    /**
     * Get folders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Set folder
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFolder $folder
     * @return IdkFinderFolder
     */
    public function setFolder(AttachableFolder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \Idk\FormBundle\Entity\IdkFinderFolder 
     */
    public function getFolder()
    {
        return $this->folder;
    }

    public function setClass($class){
        $this->class = $class;
        return $this;
    }

    public function setRealClass($class){
        $this->realClass = $class;
        return $this;
    }

    public function setItemId($itemId){
        $this->itemId = $itemId;
        return $this;
    }

    public function setZoneCode($zoneCode){
        $this->zoneCode = $zoneCode;
        return $this;
    }

    public function getZoneCode(){
        return $this->zoneCode;
    }
}
