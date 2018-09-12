<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\GenMessageBase;
use Cotars\Protoc\Plugins\Objc\ObjcTrait;
use Exception;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Label as FiledLable;
class GenMessageM extends GenMessageBase
{
    use ObjcTrait;

    public function generate(): void
    {
        $descriptor = $this->parser->getDescriptor($this->message);
        $this->pushLine(sprintf(
            '@implementation %s %s',
            $this->getObjectName($descriptor->getNamespace()),
            $this->getDoc($this->message)
        ));
        foreach ($this->message->getField() as $field) {
            list($type, $gc) = $this->getFieldType($field);
            $fieldName = $this->getPropName($field->getName());
            if ($fieldName !== $field->getName()) {
                //keyword
                $this->pushLine('');
                $this->pushLine('+ (NSDictionary *)mj_replacedKeyFromPropertyName {');
                $this->pushLine(sprintf(
                    'return @{@"%s" : @"%s"};',
                    $fieldName,
                    $field->getName()
                ), 1);
                $this->pushLine('}');
            }
            $isObject = strpos($type, '.') !== false;
            if ($field->getLabel() === FiledLable::LABEL_REPEATED && $isObject) {
                $this->pushLine('');
                $this->pushLine('+ (NSDictionary *)mj_objectClassInArray {');
                $this->pushLine(sprintf(
                    'return @{@"%s" : [%s class]};',
                    $fieldName,
                    $this->getObjectName($type)
                ), 1);
                $this->pushLine('}');
            }
        }
        $this->pushLine('@end');
    }

}