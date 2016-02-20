<?php

namespace Alchemy\Phraseanet\Mapping;

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
        foreach ($map as $definition => $subdefinition) {
            $this->addMapping($definition, $subdefinition);
        }
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
     * @param string|array $subDefinition
     */
    public function addMapping($definition, $subDefinition)
    {
        if (!  is_array($subDefinition)) {
            $subDefinition = [ 'default' => $subDefinition ];
        }

        $this->map[$definition] = $subDefinition;
    }

    /**
     * @param string $definition
     * @param string|null $type
     * @return bool
     */
    public function hasSubDefinition($definition, $type = null)
    {
        if ($type !== null && isset($this->map[$definition][$type])) {
            return true;
        }

        return isset($this->map[$definition]['default']);
    }

    /**
     * @param string $definition
     * @param string|null $type
     * @return string
     */
    public function getSubDefinition($definition, $type = null)
    {
        if (! $this->hasSubDefinition($definition, $type)) {
            throw new \OutOfBoundsException(sprintf('No subdef configured for definition "%s".', $definition));
        }

        if ($type !== null && isset($this->map[$definition][$type])) {
            return $this->map[$definition][$type];
        }

        return $this->map[$definition]['default'];
    }
}
