<?php

namespace Cotars\Protoc\Plugins;

use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;

abstract class Builder
{
    /**
     * @var CodeGeneratorRequest
     */
    protected $request;

    /**
     * @var CodeGeneratorResponse
     */
    protected $response;
    public function __construct(
        CodeGeneratorRequest $request,
        CodeGeneratorResponse $response
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    abstract public function build(): CodeGeneratorResponse;
}