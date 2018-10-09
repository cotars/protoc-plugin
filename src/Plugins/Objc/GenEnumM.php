<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\GenEnumBase;
use Cotars\Protoc\Plugins\Objc\ObjcTrait;
use Exception;
use Google\Protobuf\Internal\EnumValueDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

class GenEnumM extends GenEnumBase
{
    use ObjcTrait;
    public function generate(): void
    {
        $descriptor = $this->parser->getDescriptor($this->enum);
        $namespace = $this->getObjectName($descriptor->getParent()->getNamespace());
        // $this->pushLine(sprintf(
        //     'typedef NS_ENUM(NSInteger, %s) {',
        //     $this->getObjectName($descriptor->getNamespace())
        // ));
        // $valueLen = count($this->enum->getValue());
        // foreach ($this->enum->getValue() as $index => $value) {
        //     $this->pushLine($this->genValue($value, $index == $valueLen - 1, $namespace), 1);
        // }
        // $this->pushLine('};');

        $this->pushLine(sprintf(
            'NSString * stringFrom%s(%s val) {',
            $this->getObjectName($descriptor->getNamespace()),
            $this->getObjectName($descriptor->getNamespace())
        ));
        $this->pushLine('NSNumber * num = @(-1);', 1);
        $this->pushLine('NSDictionary * dic = @{', 1);
        foreach ($this->enum->getValue() as $index => $value) {
            $this->pushLine(sprintf(
                '@"%s": @(%s),',
                $value->getName(),
                $this->getValueNamespace($value, $namespace)
            ), 2);
        }
        $this->pushLine('};', 1);
        $this->pushLine('num = [dic objectForKey:val];', 1);
        $this->pushLine('return num;', 1);
        $this->pushLine('}');
        $this->pushLine('');
        $this->pushLine(sprintf(
            'NSNumber * %sFromString(NSString * val) {',
            $this->getObjectName($descriptor->getNamespace())
        ));
        $this->pushLine('NSString * str = @"";', 1);
        $this->pushLine('NSDictionary * dic = @{', 1);
        foreach ($this->enum->getValue() as $index => $value) {
            $this->pushLine(sprintf(
                '@(%s): @"%s",',
                $this->getValueNamespace($value, $namespace),
                $value->getName()
            ), 2);
        }
        $this->pushLine('};', 1);
        $this->pushLine('str = [dic objectForKey:@(val)];', 1);
        $this->pushLine('return str;', 1);
        $this->pushLine('}');
        $this->pushLine('');
        $this->pushLine('');
    }

    protected function getValueNamespace(EnumValueDescriptorProto $value, $namespace)
    {
        return sprintf(
            '%s_%s',
            $namespace,
            $value->getName()
        );
    }
}