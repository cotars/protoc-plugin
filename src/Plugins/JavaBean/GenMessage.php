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
            $this->pushLine($this->genField($filed), 1);
        }
        $this->pushLine('}');
    }

    protected function mapNested(DescriptorProto $nested)
    {
        if (!$nested->getOptions()->getMapEntry()) {
            return;
        }
        array_push($this->mapEntrys, $nested->getName());
    }

    protected function genField(FieldDescriptorProto $field)
    {
        $javaType = $this->getFiledType($field);
        $this->writeDoc($field, 1);
        if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
            //is map field
            if ($this->isMapFiled($field)) {
                $keyTypeName = $this->getFiledType($this->getMapKeyField($field));
                $valueTypeName = $this->getFiledType($this->getMapValueField($field));
                return sprintf(
                    'public Map<%s, %s> %s;',
                    $keyTypeName,
                    $valueTypeName,
                    $field->getName()
                );
            } else {
                return sprintf('public List<%s> %s;', $javaType, $field->getName());
            }
        } else {
            return sprintf('public %s %s;', $javaType, $field->getName());
        }
    }

}