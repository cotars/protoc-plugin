<?php

namespace Cotars\Protoc\Plugins\JavaBean;

use Cotars\Protoc\Plugins\GenMessageBase;
use Cotars\Protoc\Plugins\JavaBean\JavaTrait;
use Exception;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Label as FiledLable;
class GenMessage extends GenMessageBase
{
    use JavaTrait;
    
    public function generate(): void
    {
        $this->pushLine('/**');
        $this->pushLine(' * file:'.$this->file->getName());
        $this->pushLine(' */');
        $this->pushLine(sprintf(
            'package %s;',
            $this->file->getPackage()
        ));
        $this->pushLine('');
        $this->pushLine('import java.util.*;');
        $this->pushLine('');
        $this->writeDoc($this->message);
        $this->pushLine(sprintf(
            'public class %s {',
            ucfirst($this->message->getName())
        ));
        foreach ($this->message->getField() as $filed) {
            $this->pushLine('');
            $this->writeDoc($filed, 1);
            $this->pushLine($this->genField($filed), 1);
        }
        $this->pushLine('');
        $this->pushLine('public static class Builder {', 1);
        foreach ($this->message->getField() as $filed) {
            $this->pushLine($this->genField($filed, 'private'), 2);
        }
        $this->pushLine('public Builder() {', 2);
        $this->pushLine('}', 2);

        foreach ($this->message->getField() as $filed) {
            $this->pushFieldBuildMethod($filed);
        }

        $this->pushLine(sprintf(
            'public %s build() {',
            ucfirst($this->message->getName())
        ), 2);
        $this->pushLine(sprintf(
            '%s c = new %s();',
            ucfirst($this->message->getName()),
            ucfirst($this->message->getName())
        ), 3);
        foreach ($this->message->getField() as $filed) {
            $this->pushLine(sprintf(
                'c.%s = %s;',
                $filed->getName(),
                $filed->getName()
            ), 3);
        }
        $this->pushLine('return c;', 3);
        $this->pushLine('}', 2);
        $this->pushLine('}', 1);
        $this->pushLine('}');
    }

    protected function pushFieldBuildMethod(FieldDescriptorProto $field)
    {
        $javaType = $this->getFieldType($field);
        $this->writeDoc($field, 2);
        $this->pushLine(sprintf(
            'public Builder set%s(%s) {',
            ucfirst($field->getName()),
            $this->genFieldParam($field)
        ), 2);
        $this->pushLine(sprintf(
            'this.%s = %s;',
            $field->getName(),
            $field->getName()
        ), 3);
        $this->pushLine('return this;', 3);
        $this->pushLine('}', 2);
    }

    protected function mapNested(DescriptorProto $nested)
    {
        if (!$nested->getOptions()->getMapEntry()) {
            return;
        }
        array_push($this->mapEntrys, $nested->getName());
    }

    protected function genFieldParam(FieldDescriptorProto $field)
    {
        $javaType = $this->getFieldType($field);
        if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
            //is map field
            if ($this->isMapFiled($field)) {
                $keyTypeName = $this->getFieldType($this->getMapKeyField($field));
                $valueTypeName = $this->getFieldType($this->getMapValueField($field));
                return sprintf(
                    'Map<%s, %s> %s',
                    $decorate,
                    $keyTypeName,
                    $valueTypeName,
                    $field->getName()
                );
            } else {
                return sprintf('List<%s> %s', $javaType, $field->getName());
            }
        } else {
            return sprintf('%s %s', $javaType, $field->getName());
        }
    }


    protected function genField(FieldDescriptorProto $field, $decorate = 'public')
    {
        $javaType = $this->getFieldType($field);
        if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
            //is map field
            if ($this->isMapFiled($field)) {
                $keyTypeName = $this->getFieldType($this->getMapKeyField($field));
                $valueTypeName = $this->getFieldType($this->getMapValueField($field));
                return sprintf(
                    '%s Map<%s, %s> %s;',
                    $decorate,
                    $keyTypeName,
                    $valueTypeName,
                    $field->getName()
                );
            } else {
                return sprintf('%s List<%s> %s;', $decorate, $javaType, $field->getName());
            }
        } else {
            return sprintf('%s %s %s;', $decorate, $javaType, $field->getName());
        }
    }

}