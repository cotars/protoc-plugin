<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\GenMessageBase;
use Cotars\Protoc\Plugins\Objc\ObjcTrait;
use Exception;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Label as FiledLable;
class GenMessageH extends GenMessageBase
{
    use ObjcTrait;
    protected $classLines = [];

    public function generate(): void
    {
        $descriptor = $this->parser->getDescriptor($this->message);
        $classes = [];
        $lines = [];
        foreach ($this->message->getField() as $filed) {
            list($isObject, $objectName, $line, $gc) = $this->genField($filed);
            if ($isObject && $gc != 'assign') {
                $classes[] = $objectName;
            }
            $lines[] = [$line, $filed];
        }
        $classes = array_unique($classes);
        foreach ($classes as $class) {
            $this->classLines[] = sprintf('@class %s;', $class);
        }

        $this->pushLine(sprintf(
            '@interface %s : NSObject %s',
            $this->getObjectName($descriptor->getNamespace()),
            $this->getDoc($this->message)
        ));
        foreach ($lines as list($line, $field)) {
            $this->pushLine($line.' '.$this->getDoc($field));
        }
        $this->pushLine('@end');
    }

    protected function genField(FieldDescriptorProto $field): array
    {
        list($type, $gc) = $this->getFieldType($field);
        $isObject = strpos($type, '.') !== false;
        if ($isObject) {
            $type = $this->getObjectName($type);
        }
        $fieldName = $this->getPropName($field->getName());
        // $this->writeDoc($field, 1);
        if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
            if ($gc == 'assign') {
                $line = sprintf('@property (nonatomic, strong) NSArray<%s> %s;', $type, $fieldName);
            } else {
                $line = sprintf('@property (nonatomic, strong) NSArray<%s *> * %s;', $type, $fieldName);
            }
        } else {
            if ($gc == 'assign') {
                $line = sprintf('@property (nonatomic, %s) %s %s;', $gc, $type, $fieldName);
            } else {
                $line = sprintf('@property (nonatomic, %s) %s * %s;', $gc, $type, $fieldName);
            }
        }
        // $line = $field->getType() . '->'.$line;
        return [$isObject, $type, $line, $gc];
    }

    /**
     * Get the value of classLines
     */ 
    public function getClassLines()
    {
        return $this->classLines;
    }
}