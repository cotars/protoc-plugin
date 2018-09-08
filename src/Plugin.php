<?php

namespace Cotars\Protoc;

use Cotars\Protoc\Plugins\Builder;
use Cotars\Protoc\Plugins\JavaBean;
use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;

class Plugin
{
    protected $bin = '';
    public function __construct(string $bin)
    {
        $this->bin = $bin;
    }

    public function builder(): Builder
    {
        $request = new CodeGeneratorRequest;
        $request->mergeFromString($this->bin);
        $response = new CodeGeneratorResponse;
        return new JavaBean($request, $response);
    }
}