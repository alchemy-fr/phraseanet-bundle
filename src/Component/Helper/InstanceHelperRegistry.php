<?php

namespace Alchemy\Phraseanet\Helper;

class InstanceHelperRegistry
{

    private $defaultInstance = null;

    private $instanceHelpers = [];

    public function addHelper($name, InstanceHelper $helper)
    {
        $this->instanceHelpers[$name] = $helper;
    }

    public function hasHelper($name)
    {
        return isset($this->instanceHelpers[$name]);
    }

    public function getHelper($name)
    {
        if (! $this->hasHelper($name)) {
            throw new \OutOfBoundsException();
        }

        return $this->instanceHelpers[$name];
    }

    public function setDefaultHelper($name)
    {
        $this->defaultInstance = $name;
    }

    public function getDefaultHelper()
    {
        if ($this->defaultInstance == null) {
            return reset($this->instanceHelpers);
        }

        return $this->getHelper($this->defaultInstance);
    }
}
