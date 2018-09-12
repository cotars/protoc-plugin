<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\GenEnumBase;
use Cotars\Protoc\Plugins\Objc\ObjcTrait;
use Exception;
use Google\Protobuf\Internal\EnumValueDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

class GenEnum extends GenEnumBase
{
    use ObjcTrait;
    public function generate(): void
    {
        $descriptor = $this->parser->getDescriptor($this->enum);
        $this->pushLine(sprintf(
            'typedef NS_ENUM(NSInteger, %s) {',
            $this->getObjectName($descriptor->getNamespace())
        ));
        $valueLen = count($this->enum->getValue());
        foreach ($this->enum->getValue() as $index => $value) {
            $this->pushLine($this->genValue($value, $index == $valueLen - 1), 1);
        }
        $this->pushLine('};');
    }

    protected function genValue(EnumValueDescriptorProto $value, $isEnd = false)
    {
        return sprintf(
            '%s = %d,',
            $value->getName(),
            $value->getNumber()
        );
    }
}