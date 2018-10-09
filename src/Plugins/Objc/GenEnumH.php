<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\GenEnumBase;
use Cotars\Protoc\Plugins\Objc\ObjcTrait;
use Exception;
use Google\Protobuf\Internal\EnumValueDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

class GenEnumH extends GenEnumBase
{
    use ObjcTrait;
    public function generate(): void
    {
        $descriptor = $this->parser->getDescriptor($this->enum);
        $namespace = $this->getObjectName($descriptor->getParent()->getNamespace());
        $this->pushLine(sprintf(
            'typedef NS_ENUM(NSInteger, %s) {',
            $this->getObjectName($descriptor->getNamespace())
        ));
        $valueLen = count($this->enum->getValue());
        foreach ($this->enum->getValue() as $index => $value) {
            $this->pushLine($this->genValue($value, $index == $valueLen - 1, $namespace), 1);
        }
        $this->pushLine('};');

        $this->pushLine(sprintf(
            'NSString * stringFrom%s(%s val);',
            $this->getObjectName($descriptor->getNamespace()),
            $this->getObjectName($descriptor->getNamespace())
        ));

        $this->pushLine(sprintf(
            'NSNumber * %sFromString(NSString * val);',
            $this->getObjectName($descriptor->getNamespace())
        ));

    }

    protected function genValue(EnumValueDescriptorProto $value, $isEnd = false, $namespace)
    {
        return sprintf(
            '%s_%s = %d,',
            $namespace,
            $value->getName(),
            $value->getNumber()
        );
    }
}