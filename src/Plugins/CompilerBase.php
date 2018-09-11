<?php

namespace Cotars\Protoc\Plugins;

use Cotars\Protoc\Plugins\Parser;

abstract class CompilerBase
{
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser) {
        $this->parser = $parser;
    }
    abstract public function compile();
}