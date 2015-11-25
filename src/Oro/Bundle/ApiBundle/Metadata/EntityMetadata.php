<?php

namespace Oro\Bundle\ApiBundle\Metadata;

use Oro\Component\ChainProcessor\ParameterBag;

class EntityMetadata extends ParameterBag
{
    /** FQCN of an entity */
    const CLASS_NAME = 'class';

    /** @var string[] */
    private $identifiers = [];

    /** @var FieldMetadata[] */
    private $fields = [];

    /** @var AssociationMetadata[] */
    private $associations = [];

    /**
     * Gets FQCN of an entity.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->get(self::CLASS_NAME);
    }

    /**
     * Sets FQCN of an entity.
     *
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->set(self::CLASS_NAME, $className);
    }

    /**
     * Gets identifier field names.
     */
    public function getIdentifierFieldNames()
    {
        return $this->identifiers;
    }

    /**
     * Sets identifier field names.
     *
     * @param string[] $fieldNames
     */
    public function setIdentifierFieldNames(array $fieldNames)
    {
        $this->identifiers = $fieldNames;
    }

    /**
     * Gets metadata for all fields.
     *
     * @return FieldMetadata[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Checks whether metadata of the given field exists.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * Gets metadata of a field.
     *
     * @param string $fieldName
     *
     * @return FieldMetadata|null
     */
    public function getField($fieldName)
    {
        return isset($this->fields[$fieldName])
            ? $this->fields[$fieldName]
            : null;
    }

    /**
     * Adds metadata of a field.
     *
     * @param FieldMetadata $field
     */
    public function addField(FieldMetadata $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * Removes metadata of a field.
     *
     * @param string $fieldName
     */
    public function removeField($fieldName)
    {
        unset($this->fields[$fieldName]);
    }

    /**
     * Renames existing field
     *
     * @param string $oldName
     * @param string $newName
     */
    public function renameField($oldName, $newName)
    {
        $metadata = $this->getField($oldName);
        if (null !== $metadata) {
            $this->removeField($oldName);
            $metadata->setName($newName);
            $this->addField($metadata);
        }
    }

    /**
     * Gets metadata for all associations.
     *
     * @return AssociationMetadata[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * Checks whether metadata of the given association exists.
     *
     * @param string $associationName
     *
     * @return bool
     */
    public function hasAssociation($associationName)
    {
        return isset($this->associations[$associationName]);
    }

    /**
     * Gets metadata of an association.
     *
     * @param string $associationName
     *
     * @return AssociationMetadata|null
     */
    public function getAssociation($associationName)
    {
        return isset($this->associations[$associationName])
            ? $this->associations[$associationName]
            : null;
    }

    /**
     * Adds metadata of an association.
     *
     * @param AssociationMetadata $association
     */
    public function addAssociation(AssociationMetadata $association)
    {
        $this->associations[$association->getName()] = $association;
    }

    /**
     * Removes metadata of an association.
     *
     * @param string $associationName
     */
    public function removeAssociation($associationName)
    {
        unset($this->associations[$associationName]);
    }

    /**
     * Renames existing association
     *
     * @param string $oldName
     * @param string $newName
     */
    public function renameAssociation($oldName, $newName)
    {
        $metadata = $this->getAssociation($oldName);
        if (null !== $metadata) {
            $this->removeAssociation($oldName);
            $metadata->setName($newName);
            $this->addAssociation($metadata);
        }
    }

    public function toArray()
    {
        $result = parent::toArray();

        $result['identifiers'] = $this->getIdentifierFieldNames();

        $fields = $this->getFields();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $data = $field->toArray();
                unset($data[AssociationMetadata::NAME]);
                $result['fields'][$field->getName()] = $data;
            }
        }

        $associations = $this->getAssociations();
        if (!empty($associations)) {
            foreach ($associations as $association) {
                $data = $association->toArray();
                unset($data[AssociationMetadata::NAME]);
                $result['associations'][$association->getName()] = $data;
            }
        }

        return $result;
    }
}
