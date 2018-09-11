<?php

namespace Cotars\Protoc\Plugins;

use Cotars\Protoc\Plugins\Parser;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;

abstract class GenBase
{
    /**
     * @var array
     */
    protected $content = [];

    /**
     * @var Parser
     */
    protected $parser;

    public function pushLine(string $str, $index = 0)
    {
        array_push($this->content, [$str, $index]);
    }
    /**
     * Get the value of content
     */ 
    abstract function getContent(): string;

    abstract public function generate(): void;

    /**
     * Set the value of parser
     *
     * @param  Parser  $parser
     *
     * @return  self
     */ 
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
        return $this;
    }
}