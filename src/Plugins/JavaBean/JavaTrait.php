<?php

namespace Cotars\Protoc\Plugins\JavaBean;

use Cotars\Protoc\Plugins\Parser;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

trait JavaTrait
{
    /**
     * @var Parser
     */
    protected $parser;
    public function getContent(): string
    {
        $content = '';
        foreach ($this->content as $line) {
            list($code, $tab) = $line;
             $content .= str_pad('', $tab * 4, ' ').$code . "\n";
        }
        return $content;
    }

    public function writeDoc($proto, $index = 0)
    {
        $this->writeDocHeader($index);
        $location = $this->parser->getLocation($proto);
        $doc = trim($location->getLeadingComments());
        if (!$doc) {
            $doc = trim($location->getTrailingComments());
        }
        if (!$doc) {
            $doc = $this->parser->getDescriptor($proto)->getNamespace();
        }
        $docs = explode("\n", $doc);
        foreach ($docs as $doc) {
            $this->pushLine(' * '.$doc, $index);
        }
        $this->writeDocEnd($index);
    } 

    public function writeDocHeader($index = 0)
    {
        $this->pushLine('/**', $index);
    }

    public function writeDocEnd($index = 0)
    {
        $this->pushLine(' */', $index);
    }

    public function getObjectType($javaType)
    {
        $maps = [
            'int' => 'Integer',
            'double' => 'Double',
            'float' => 'Float',
            'boolean' => 'Boolean'
        ];
        if (isset($maps[$javaType])) {
            return $maps[$javaType];
        }
        return $javaType;
    }

    public function getFieldType(FieldDescriptorProto $field): string
    {
        $javaType = null;
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
        return $javaType;
    }
}