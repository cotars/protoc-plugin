<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

abstract class GenMessageBase extends GenBase
{

    /**
     * @var DescriptorProto
     */
    protected $message;

    /**
     * @var FileDescriptorProto
     */
    protected $file;

    public function __construct(DescriptorProto $message, FileDescriptorProto $file)
    {
        $this->message = $message;
        $this->file = $file;
    }

}