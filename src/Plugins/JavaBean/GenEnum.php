<?php

namespace Cotars\Protoc\Plugins\JavaBean;

use Cotars\Protoc\Plugins\GenEnumBase;
use Cotars\Protoc\Plugins\JavaBean\JavaTrait;
use Exception;
use Google\Protobuf\Internal\EnumValueDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto_Type as FieldType;

class GenEnum extends GenEnumBase
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
        $this->writeDoc($this->enum);
        $this->pushLine(sprintf(
            'public enum %s {',
            ucfirst($this->enum->getName())
        ));
        $valueLen = count($this->enum->getValue());
        if ($valueLen > 0) {
            $this->pushLine('Unknown(-1),', 1);
        } else {
            $this->pushLine('Unknown(-1);', 1);
        }
        foreach ($this->enum->getValue() as $index => $value) {
            $this->writeDoc($value, 1);
            $this->pushLine(
                $this->genValue($value, $index == $valueLen - 1),
                1
            );
        }
        $this->pushLine('');
        $this->pushLine('public int code;', 1);
        $this->pushLine('');
        $this->pushLine(sprintf(
            '%s(int code) { this.code = code; }',
            $this->enum->getName()
        ), 1);
        $this->pushLine('');
        $this->pushLine(sprintf(
            'public static %s valueOf(final int code) {',
            $this->enum->getName()
        ), 1);
        $this->pushLine(sprintf(
            'for (%s c : %s.values()) {',
            $this->enum->getName(),
            $this->enum->getName()
        ), 2);
        $this->pushLine('if (code == c.code) return c;', 3);
        $this->pushLine('}', 2);
        $this->pushLine('return Unknown;', 2);
        $this->pushLine('}', 1);
        $this->pushLine('}');
    }

    protected function genValue(EnumValueDescriptorProto $value, $isEnd = false)
    {
        return sprintf(
            '%s(%d)%s',
            $value->getName(),
            $value->getNumber,
            $isEnd ? ';' : ','
        );
    }
}