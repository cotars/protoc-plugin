<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

abstract class GenBase
{
    /**
     * @var array
     */
    protected $content = [];

    public function pushLine(string $str, $index = 0)
    {
        array_push($this->content, [$str, $index]);
    }
    /**
     * Get the value of content
     */ 
    abstract function getContent(): string;

    abstract public function generate(): void;
}