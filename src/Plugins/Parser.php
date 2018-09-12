<?php

namespace Cotars\Protoc\Plugins;

use Cotars\Protoc\Plugins\Descriptor;
use Exception;
use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\EnumValueDescriptorProto;
use Google\Protobuf\Internal\FieldDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\SourceCodeInfo_Location as SourceCodeInfoLocation;
use SplObjectStorage;
use Cotars\Protoc\Plugins\JavaBean\Compiler;
use Google\Protobuf\Compiler\CodeGeneratorResponse_File as ResponseFile;

class Parser {
    /**
     * @var CodeGeneratorRequest
     */
    protected $request;

    /**
     * @var CodeGeneratorResponse
     */
    protected $response;
    /**
     * @var array[string]
     */
    protected $loggers = [];

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var array[string]
     */
    protected $params = [];

  
    /**
     * @var array[SourceCodeInfoLocation]
     */
    protected $locations;

    /**
     * @var SplObjectStorage
     */
    protected $descriptors = [];

    public function __construct(string $bin)
    {
        $this->request = new CodeGeneratorRequest;
        $this->request->mergeFromString($bin);
        $this->response = new CodeGeneratorResponse;
        // $this->isDebug = true;
        $this->descriptors = new SplObjectStorage;
    }

    public function parse()
    {
        $param = $this->request->getParameter();
        $this->parseParameter();
        foreach ($this->request->getFileToGenerate() as $file) {
            $this->logger('file to generate', $file);
        }
        foreach ($this->request->getProtoFile() as $i => $file) {
            $descriptor = $this->storegeProto($i, Descriptor::FILE, $file);
            $descriptor->setNamespace($file->getPackage());
            $this->expandFile($file, $descriptor);
        }
        $type = $this->getParameter('type');
        $compiler = null;
        switch ($type) {
            case 'javabean':
                $compiler = \Cotars\Protoc\Plugins\JavaBean\Compiler::class;
                break;
            case 'objc':
                $compiler = \Cotars\Protoc\Plugins\Objc\Compiler::class;
                break;
            default:
                throw new Exception('type not support->'.$type);
        }
        $compiler = new $compiler($this);
        $compiler->compile();
    }

    public function storegeProto($index, $type, $proto, ?Descriptor $parent = null): Descriptor
    {
        $descriptor = new Descriptor($parent);
        $descriptor->setIndex($index)->setType($type);
        $this->descriptors->attach($proto, $descriptor);
        return $descriptor;
    }

    public function getParameter(string $key, $default = null): string
    {
        $val = $this->params[$key] ?? '';
        if (!$val && $default === null) {
            throw new Exception('not found parameter -> ' . $key);
        }
        return $val;
    }

    public function expandFile(FileDescriptorProto $file, Descriptor $descriptor)
    {
        $this->logger(
            'file proto', 'name(%s), syntax(%s), package(%s)',
            $file->getName(),
            $file->getSyntax(),
            $file->getPackage()
        );
        // $this->files->attach($file);
        foreach ($file->getMessageType() as $index => $message) {
            $newDescriptor = $this->storegeProto($index, Descriptor::MESSAGE, $message, $descriptor);
            $this->expandMessage($message, $newDescriptor);
        }
        foreach ($file->getEnumType() as $index=>$enum) {
            $newDescriptor = $this->storegeProto($index, Descriptor::ENUM, $enum, $descriptor);
            $this->expandEnum($enum, $newDescriptor);
        }

        foreach ($file->getSourceCodeInfo()->getLocation() as $location) {
            $this->expandLocation($location, $descriptor);
        }
    }

    public function expandLocation(SourceCodeInfoLocation $location, Descriptor $descriptor)
    {
        $path = [];
        $path[] = $descriptor->getType();
        $path[] = $descriptor->getIndex();
        foreach ($location->getPath() as $v) {
            $path[] = $v;
        }
        // $this->logger('pathxxx', implode(',', $path).$location->getLeadingComments().'@'.$location->getTrailingComments());
        $this->locations[implode(',', $path)] = $location;
    }

    public function getDescriptor($object):Descriptor
    {
        return $this->descriptors[$object];
    }


    public function getLocation($object): SourceCodeInfoLocation
    {
        $descriptor = $this->getDescriptor($object);
        $path = $descriptor->getPath();
        // $this->logger('path', $path);
        return $this->locations[$path];
    }

    public function expandMessage(
        DescriptorProto $message,
        ?Descriptor $descriptor = null
    ) {
        // $this->files->attach($message, [$file, $parent]);
        foreach ($message->getField() as $index=>$field) {
            $newDescriptor = $this->storegeProto($index, Descriptor::FIELD, $field, $descriptor);
            $newDescriptor->setNamespace($field->getName());
        }
        $descriptor && $descriptor->setNamespace($message->getName());
        foreach ($message->getNestedType() as $index=>$nested) {
            $newDescriptor = $this->storegeProto($index, Descriptor::NESTED, $nested, $descriptor);
            $this->expandMessage($nested, $newDescriptor);
        }
        foreach ($message->getEnumType() as $index=>$enum) {
            $newDescriptor = $this->storegeProto($index, Descriptor::NESTED, $enum, $descriptor);
            $this->expandEnum($enum, $newDescriptor);
        }
    }

    public function expandEnum(
        EnumDescriptorProto $enum,
        ?Descriptor $descriptor = null
    ) {
        $descriptor && $descriptor->setNamespace($enum->getName());
        // $this->files->attach($enum, [$file, $parent]);
        foreach ($enum->getValue() as $index=>$value) {
            $newDescriptor = $this->storegeProto($index, Descriptor::ENUM_VALUE, $value, $descriptor);
            $newDescriptor->setNamespace($value->getName());
        }
    }

    // public function expandEnumValue(EnumDescriptorProto $enum, EnumValueDescriptorProto $value)
    // {
    //     // $this->files->attach($value, $enum);
    // }

    // public function expandFiled(DescriptorProto $message, FieldDescriptorProto $field)
    // {
    //     // $this->files->attach($field, $message);
    // }

    public function parseParameter(): void
    {
        $lists = explode(',', $this->request->getParameter());
        foreach ($lists as $v) {
            $p = explode('=', $v, 2);
            $name = $p[0];
            $this->params[$name] = $p[1];
            $this->logger('param', $v);
        }
    }

    public function getRequest(): CodeGeneratorRequest
    {
        return $this->request;
    }

    public function getResponse($isExeption = false): CodeGeneratorResponse
    {
        if (!$isExeption && $this->isDebug && $this->loggers) {
            $this->response = new CodeGeneratorResponse;
            $this->response->setError("\n".implode("\n", $this->loggers));
        }
        return $this->response;
    }

    public function setDebug(bool $debug)
    {
        $this->isDebug = $debug;
    }

    public function logger(string $type, ...$args)
    {
        $message = sprintf('%s:%s', $type, call_user_func_array('sprintf', $args));
        array_push($this->loggers, $message);
    }

    public function appendCompiled(ResponseFile $file)
    {
        $this->logger('compiled file', $file->getName());
        $this->response->getFile()[] = $file; 
    }

    /**
     * Get the value of params
     *
     * @return  array[string]
     */ 
    public function getParams()
    {
        return $this->params;
    }
}