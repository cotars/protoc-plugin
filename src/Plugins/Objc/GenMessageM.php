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

        $keywords = [];
        $classes = [];

        foreach ($this->message->getField() as $field) {
            list($type, $gc) = $this->getFieldType($field);
            $fieldName = $this->getPropName($field->getName());
            if ($fieldName !== $field->getName()) {
                // //keyword
                // $this->pushLine('');
                // $this->pushLine('+ (NSDictionary *)mj_replacedKeyFromPropertyName {');
                // $this->pushLine(sprintf(
                //     'return @{@"%s" : @"%s"};',
                //     $fieldName,
                //     $field->getName()
                // ), 1);
                $keywords[] = [$fieldName, $field->getName()];
                // $this->pushLine('}');
            }
            $isObject = strpos($type, '.') !== false;
            if ($field->getLabel() === FiledLable::LABEL_REPEATED && $isObject) {
                $classes[] = [$fieldName, $this->getObjectName($type)];
                // $this->pushLine('');
                // $this->pushLine('+ (NSDictionary *)mj_objectClassInArray {');
                // $this->pushLine(sprintf(
                //     'return @{@"%s" : [%s class]};',
                //     $fieldName,
                //     $this->getObjectName($type)
                // ), 1);
                // $this->pushLine('}');
            }
        }
        // print_r();
        if ($keywords) {
            $this->pushLine('');
            $this->pushLine('+ (NSDictionary *)mj_replacedKeyFromPropertyName {');
            $keyLens = count($keywords);
            foreach ($keywords as $i => $v) {
                list($fieldName, $field) = $v;
                $line = '';
                $index = 1;
                if ($i < 1) {
                    $line .= 'return @{';
                } else {
                    $index = 2;
                }
                $line .= sprintf('@"%s" : @"%s"', $fieldName, $field);
                if ($keyLens > 1 && $i < $keyLens - 1) {
                    $line .= ',';
                }
                if ($i == $keyLens - 1) {
                    $line .= '};';
                }
                $this->pushLine($line, $index);
            }
            $this->pushLine('}');
        }
        if ($classes) {
            $this->pushLine('');
            $this->pushLine('+ (NSDictionary *)mj_objectClassInArray {');
            $keyLens = count($classes);
            foreach ($classes as $i => $v) {
                list($fieldName, $objectName) = $v;
                $line = '';
                $index = 1;
                if ($i < 1) {
                    $line .= 'return @{';
                } else {
                    $index = 2;
                    $line .= '     ';
                }
                $line .= sprintf('@"%s" : [%s class]', $fieldName, $objectName);
                if ($keyLens > 1 && $i < $keyLens - 1) {
                    $line .= ',';
                }
                if ($i === $keyLens - 1) {
                    $line .= '};';
                }
                $this->pushLine($line, $index);
            }
            $this->pushLine('}');
        }
        $this->pushLine('@end');
    }

}