<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

class Descriptor
{
    const FILE = 0;
    const MESSAGE = 4;
    const ENUM = 5;
    const NESTED = 3;
    const FIELD = 2;
    const NESTED_ENUM = 4;
    const ENUM_VALUE = 2;
    /**
     * @var Descriptor
     */
    protected $parent;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var string
     */
    protected $namespace = '';

    public function __construct(?Descriptor $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get the value of type
     *
     * @return  int
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  int  $type
     *
     * @return  self
     */ 
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of index
     *
     * @return  int
     */ 
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @param  int  $index
     *
     * @return  self
     */ 
    public function setIndex(int $index)
    {
        $this->index = $index;

        return $this;
    }

    public function getPath(): string
    {
        $parent = $this->parent ? $this->parent->getPath() : '';
        return trim(sprintf(
            '%s,%d,%d',
            $parent,
            $this->type,
            $this->index
        ), ',');
    }

    public function getNamespace(): string
    {
        $parent = $this->parent ? $this->parent->getNamespace() : '';
        return trim(sprintf(
            '%s.%s',
            $parent,
            $this->namespace
        ), '.');
    }

    /**
     * Set the value of namespace
     *
     * @param  string  $namespace
     *
     * @return  self
     */ 
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get the value of parent
     *
     * @return  Descriptor
     */ 
    public function getParent()
    {
        return $this->parent;
    }
}