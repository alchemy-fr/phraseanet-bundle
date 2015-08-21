<?php

namespace Alchemy\Phraseanet;

/**
 * Simple class to map friendly names to Phraseanet media sub-definitions.
 *
 * @package Alchemy\Phraseanet
 */
class DefinitionMap
{
    /**
     * @var array
     */
    private $map;

    /**
     * @param array $map Initial mapping using friendly names as keys and matching sub-definitions as values
     */
    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * @return array
     * @deprecated Use DefinitionMap::toArray() instead
     */
    public function getMap()
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        return $this->map;
    }

    /**
     * @param string $definition
     * @param string $subDefinition
     */
    public function addMapping($definition, $subDefinition)
    {
        $this->map[$definition] = $subDefinition;
    }

    /**
     * @param $definition
     * @return string
     * @throws \Exception
     * @deprecated Use DefinitionMap::getSubDefinition() instead
     */
    public function getDefinitionSubdef($definition)
    {
        return $this->getSubDefinition($definition);
    }

    /**
     * @param $definition
     * @return string
     * @throws \Exception
     */
    public function getSubDefinition($definition)
    {
        if (!isset($this->map[$definition])) {
            throw new \OutOfBoundsException(sprintf('No subdef configured for definition "%s".', $definition));
        }

        return $this->map[$definition];
    }
}
