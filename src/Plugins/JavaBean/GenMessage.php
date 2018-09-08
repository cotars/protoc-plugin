<?php

namespace Cotars\Protoc\Plugins\JavaBean;

use Cotars\Protoc\Plugins\GenMessageBase;
use Cotars\Protoc\Plugins\JavaBean\JavaTrait;
use Exception;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

class GenMessage extends GenMessageBase
{
    use JavaTrait;
    public function generate(): void
    {
        $this->pushLine(sprintf(
            'package %s;',
            $this->file->getPackage()
        ));
        $this->pushLine('');
        $this->pushLine(sprintf(
            'public class %s {',
            ucfirst($this->message->getName())
        ));
        foreach ($this->message->getField() as $filed) {
            $this->pushLine($this->genField($filed), 1);
        }
        $this->pushLine('}');
    }

    protected function genField(FieldDescriptorProto $field)
    {
        $javaType = '';
        switch ($field->getType()) {
            case FieldType::TYPE_INT32:
            case FieldType::TYPE_UINT32:
            case FieldType::TYPE_SINT32:
            case FieldType::TYPE_FIXED32:
            case FieldType::TYPE_SFIXED32:
                $javaType = 'int';
                break;
            
            case FieldType::TYPE_INT64:
            case FieldType::TYPE_UINT64:
            case FieldType::TYPE_SINT64:
            case FieldType::TYPE_FIXED64:
            case FieldType::TYPE_SFIXED64:
                $javaType = 'long';
                break;
            case FieldType::TYPE_DOUBLE:
                $javaType = 'double';
                break;
            case FieldType::TYPE_FLOAT:
                $javaType = 'float';
                break;
            case FieldType::TYPE_BOOL:
                $javaType = 'boolean';
                break;
            case FieldType::TYPE_STRING:
                $javaType = 'String';
                break;
            case FieldType::TYPE_BYTES:
                $javaType = 'ByteString';
                break;
            case FieldType::TYPE_ENUM:
            case FieldType::TYPE_MESSAGE:
                $javaType = trim($field->getTypeName(), '.');
                break;
            default:
                throw new Exception("not support type".$field->getType());
        }
        return sprintf('public %s %s;', $javaType, $field->getName());
    }

}