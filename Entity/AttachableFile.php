<?php

namespace Idk\LegoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Site
 *
 * @ORM\Table(name="attachable_file")
 * @ORM\Entity(repositoryClass="Idk\LegoBundle\Entity\AttachableFileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AttachableFile
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
     * @var string
     *
     * @ORM\Column(name="fichier", type="string", length=255)
     */
    private $fichier;

     /**
     * @var integer
     *
     * @ORM\Column(name="taille", type="integer", nullable=true)
     */
    private $taille;
     /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
     /**
     * @var string
     *
     * @ORM\Column(name="sub_type", type="string", length=255, nullable=true)
     */
    private $subType;

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
     * @var string
     *
     * @ORM\Column(name="path", type="text", nullable=true)
     */
    private $path;


    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="AttachableFolder", inversedBy="files")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $folder;

    /**
     * @var integer
     *
     * @ORM\Column(name="zone_code", type="string", nullable=true)
     */
    private $zoneCode;

    private $tempFilename;

    

    public function __construct(){
    }

    private function getClassDir(){
        return str_replace('\\','_',$this->realClass);
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
     * set id
     *
     * @param integer $id
     * @return Mandat
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Set nom
     *
     * @param string $nom
     * @return IdkFinderFile
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
     * @return IdkFinderFile
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
     * Set folder
     *
     * @param \Idk\FormBundle\Entity\IdkFinderFolder $folder
     * @return IdkFinderFile
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

    /**
     * Set type
     *
     * @param string $type
     * @return IdkFinderFile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subType
     *
     * @param string $subType
     * @return IdkFinderFile
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * Get subType
     *
     * @return string 
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Set fichier
     *
     * @param string $fichier
     * @return IdkFinderFile
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string 
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * Set taille
     *
     * @param integer $taille
     * @return IdkFinderFile
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille
     *
     * @return integer 
     */
    public function getTaille()
    {
        return $this->taille;
    }

    public function getUrl(){
        return '/uploads/LleAttachable/'.$this->getClassDir().$this->getPath().'/'.urlencode($this->getFichier());
    }

    public function getRealPath(){
         return $this->getUploadDir().'/'.$this->getFichier();
    }



    public function getPreview(){
        if($this->getType() == 'image'){
            return $this->getUrl();
        } else {
            return '/bundles/lleadminlist/images/attachement_preview.png';
        }
        
    }

    /**
    * @ORM\PreRemove()
    */
    public function preRemove(){
        $this->tempFilename = $this->getUploadDir().'/'.$this->fichier;
    }

    /**
    * @ORM\PostRemove()
    */
    public function postRemove(){
        $this->deleteTempFile();
    }

    public function deleteTempFile(){
        if($this->tempFilename !== null){
            if(file_exists($this->tempFilename)){
                unlink($this->tempFilename);
            }
        }
    }
    public function getUploadDir(){
        return  __DIR__.'/../../../../../../web/uploads/LleAttachable/'.$this->getClassDir().$this->getPath();
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

    public function getPath(){
        if($this->path == null && $this->zoneCode) return '/'.$this->zoneCode.'/'.$this->itemId;
        if($this->path == null) return '/'.$this->itemId;
        return $this->path;
    }

    public function setPath($path){
        $this->path = $path;
        return $this;
    }

    public function setZoneCode($zoneCode){
        $this->zoneCode = $zoneCode;
        return $this;
    }

    public function getZoneCode(){
        return $this->zoneCode;
    }

    public function getItemId(){
        return $this->itemId;
    }

    public function getRealClass(){
        return $this->realClass;
    }

    public function isType($type){
        return ($this->getType() == $type or $this->getSubType() == $type);
    }


    public function duplicate()
    {
        $item = clone $this;
        $item->setId(null);
        return $item;
    }
}
