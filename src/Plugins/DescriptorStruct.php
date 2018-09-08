<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\EnumDescriptor;

class DescriptorStruct
{

    /**
     * @var int
     */
    protected $index;

    /**
     * @var Descriptor
     */
    protected $parent;


    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $group;

    /**
     * @var array[Descriptor]
     */
    protected $nested;

    /**
     * @var array[EnumDescriptor]
     */
    protected $enums;

    /**
     * @var array[string]
     */
    protected $typeName;

    public function setParent(Descriptor $Descriptor): Descriptor
    {
        return $this;
    }

    public function getParent(): Descriptor
    {
        return $this->parent;
    }

    /**
     * Get the value of index
     *
     * @return  int
     */ 
    public function getIndex(): int
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

    /**
     * Get the value of path
     *
     * @return  string
     */ 
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param  string  $path
     *
     * @return  self
     */ 
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of group
     *
     * @return  bool
     */ 
    public function getGroup(): bool
    {
        return $this->group;
    }

    /**
     * Set the value of group
     *
     * @param  bool  $group
     *
     * @return  self
     */ 
    public function setGroup(bool $group)
    {
        $this->group = $group;
        return $this;
    }

    public function appendEnumDescriptor(EnumDescriptor $enum)
    {
        array_push($this->enums, $enum);
        return $this;
    }

    public function appendNested(EnumDescriptor $nested)
    {
        array_push($this->nested, $nested);
        return $this;
    }

    /**
     * Get the value of nested
     *
     * @return  array[Descriptor]
     */ 
    public function getNested(): array
    {
        return $this->nested;
    }

    /**
     * Get the value of enums
     *
     * @return  array[EnumDescriptor]
     */ 
    public function getEnums(): array
    {
        return $this->enums;
    }
}