<?php

namespace Cotars\Protoc\Plugins;

use ArrayObject;
use Google\Protobuf\Compiler\CodeGeneratorResponse;

class Params extends ArrayObject
{
    /**
     * @var string
     */
    protected $paramString;
    public function __construct(string $param)
    {
        $this->paramString = $param;
        $this->decode();
    }

    public function decode()
    {
        $lists = explode(',', $this->paramString);
        
        foreach ($lists as $v) {
            $p = explode('=', $v, 2);
            $name = $p[0];
            $this->$name = $p[1];
        }
    }
}