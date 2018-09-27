<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\Parser;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;
use Google\Protobuf\Internal\FieldDescriptorProto_Label as FiledLable;

trait ObjcTrait
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

    public function getObjectName($package)
    {
        $params = $this->parser->getParams();
        foreach ($params as $p => $v) {
            if (strrpos($p, 'prefix_') === 0) {
                $n = substr($p, 7);
                $package = str_replace($v, $n, $package);
            }
        }
        //todo 替换
        return str_replace(
            ' ',
            '',
            ucwords(str_replace('.', ' ', $package))
        );
    }

    public function getDoc($proto)
    {
        $location = $this->parser->getLocation($proto);
        $doc = trim($location->getLeadingComments());
        if (!$doc) {
            $doc = trim($location->getTrailingComments());
        }
        if (!$doc) {
            $doc = $this->parser->getDescriptor($proto)->getNamespace();
        }
        $docs = str_replace("\n", ', ', $doc);
        return sprintf('/**< %s*/', $docs);
    }

    public function getPropName($name)
    {
        $keywords = ['id', 'float', 'double', 'bool', 'NULL', 'template', 'description'];
        if (in_array($name, $keywords)) {
            return $name . '_';
        } else {
            return $name;
        }
    }

    public function getFieldType(FieldDescriptorProto $field): array
    {
        $type = null;
        $gc = 'strong';
        switch ($field->getType()) {
            case FieldType::TYPE_INT32:
            case FieldType::TYPE_INT64:
            case FieldType::TYPE_SINT32:
            case FieldType::TYPE_SINT64:
            case FieldType::TYPE_SFIXED64:
            case FieldType::TYPE_SFIXED32:
                if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
                    $type = 'NSNumber';
                } else {
                    $type = 'NSInteger';
                    $gc = 'assign';
                }
                break;
            
            case FieldType::TYPE_UINT32:
            case FieldType::TYPE_UINT64:
            case FieldType::TYPE_FIXED32:
            case FieldType::TYPE_FIXED64:
                if ($field->getLabel() === FiledLable::LABEL_REPEATED) {
                    $type = 'NSNumber';
                } else {
                    $type = 'NSUInteger';
                    $gc = 'assign';
                }
                break;
            case FieldType::TYPE_DOUBLE:
            case FieldType::TYPE_FLOAT:
                $gc = 'assign';
                $type = 'CGFloat';
                break;
            case FieldType::TYPE_BOOL:
                $gc = 'assign';
                $type = 'BOOL';
                break;
            case FieldType::TYPE_STRING:
            case FieldType::TYPE_BYTES:
                $type = 'NSString';
                break;
            case FieldType::TYPE_ENUM:
                $type = trim($field->getTypeName(), '.');
                $gc = 'assign';
                break;
            case FieldType::TYPE_MESSAGE:
                $type = trim($field->getTypeName(), '.');
                break;
            default:
                throw new Exception("not support type".$field->getType());
        }
        return [$type, $gc];
    }
}