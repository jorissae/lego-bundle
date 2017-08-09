<?php
namespace Lle\AdminListBundle\Form\Transformer;


use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class ObjectToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $em;
    /**
     * @var string
     */
    private $class;
    /**
     * @param ManagerRegistry $registry
     * @param string          $class
     */
    public function __construct($em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }
    /**
     * Transforms an object (object) to a string (id).
     *
     * @param  Object|null $object
     * @return string
     */
    public function transform($object)
    {
        if (null === $object) {
            return '';
        }
        return $object->getId();
    }
    /**
     * Transforms a string (id) to an object (object).
     *
     * @param  string                        $id
     * @return Object|null
     * @throws TransformationFailedException if object (object) is not found.
     */
    public function reverseTransform($id)
    {
        if (empty($id)) {
            return;
        }
        $object = $this->em->getRepository($this->class)->find($id);
        if (null === $object) {
            throw new TransformationFailedException(sprintf('Object from class %s with id "%s" not found', $this->class, $id));
        }
        return $object;
    }
}