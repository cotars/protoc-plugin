<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

abstract class GenEnumBase extends GenBase
{
    /**
     * @var EnumDescriptorProto
     */
    protected $enum;

    /**
     * @var FileDescriptorProto
     */
    protected $file;

    public function __construct(EnumDescriptorProto $enum, FileDescriptorProto $file)
    {
        $this->enum = $enum;
        $this->file = $file;
    }
}