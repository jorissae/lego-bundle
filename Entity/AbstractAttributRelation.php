<?php 
namespace Idk\LegoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Attribut
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractAttributRelation{


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @var string valeur = valeur* selon typeDonne Integer,String,Bool,List(attribut_choix.id),Float
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    protected $value;

    abstract function getItem();
    abstract function getAttribut();

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
     * Set value
     *
     * @param string $value
     * @return BienAttribut
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

}