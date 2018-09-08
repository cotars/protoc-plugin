<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

abstract class GenMessageBase extends GenBase
{

    /**
     * @var DescriptorProto
     */
    protected $message;

    /**
     * @var FileDescriptorProto
     */
    protected $file;

    /**
     * @var array
     */
    protected $mapEntrys = [];
    public function __construct(DescriptorProto $message, FileDescriptorProto $file)
    {
        $this->message = $message;
        $this->file = $file;
        foreach ($this->message->getNestedType() as $nested) {
            $option = $nested->getOptions();
            if ($option && $option->hasMapEntry()) {
                $name = sprintf(
                    '.%s.%s.%s',
                    $file->getPackage(),
                    $message->getName(),
                    $nested->getName()
                );
                $this->mapEntrys[$name] = $nested;
            }
        }
    }

    public function getMapKeyField(FieldDescriptorProto $field): FieldDescriptorProto
    {
        $mapFields = $this->mapEntrys[$field->getTypeName()]->getField();
        foreach ($mapFields as $mapField) {
            if ($mapField->getName() === 'key') {
                return $mapField;
            }
        }
    }

    public function getMapValueField(FieldDescriptorProto $field): FieldDescriptorProto
    {
        $mapFields = $this->mapEntrys[$field->getTypeName()]->getField();
        foreach ($mapFields as $mapField) {
            if ($mapField->getName() === 'value') {
                return $mapField;
            }
        }
    }

    public function isMapFiled(FieldDescriptorProto $field)
    {
        if (isset($this->mapEntrys[$field->getTypeName()])) {
            return true;
        }
        return false;
    }
}