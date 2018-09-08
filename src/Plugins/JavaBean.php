<?php

namespace Cotars\Protoc\Plugins;

use Cotars\Protoc\Plugins\JavaBean\GenEnum;
use Cotars\Protoc\Plugins\JavaBean\GenMessage;
use Google\Protobuf\Compiler\CodeGeneratorResponse;
use Google\Protobuf\Compiler\CodeGeneratorResponse_File as ResponseFile;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\MethodDescriptorProto;
use Google\Protobuf\Internal\ServiceDescriptorProto;
use RuntimeException;
class JavaBean extends Builder
{
    protected $descriptors = [];
    public function build(): CodeGeneratorResponse
    {
        $fileToGen = $this->request->getFileToGenerate();
        foreach ($fileToGen as $v) {
            // print_r($v);
        }
        $param = new JavaBeanParams($this->request->getParameter());

        $this->wrapTypes();

        return $this->response;
    }

    protected function wrapTypes()
    {
        foreach ($this->request->getProtoFile() as $file) {
            $this->wrapType($file);
        }
    }

    protected function wrapType(FileDescriptorProto $file)
    {
        foreach ($file->getMessageType() as $i => $descriptor) {
            $this->wrapDescriptor($descriptor, $file, $i);
        }

        foreach ($file->getEnumType() as $i => $descriptor) {
            $this->wrapEnumDescriptor($descriptor, $file, $i);
        }

        foreach ($file->getService() as $i => $descriptor) {
            $this->wrapService($descriptor, $file, $i);
        }
    }

    protected function wrapService(
        ServiceDescriptorProto $descriptor,
        FileDescriptorProto $file,
        $index
    ) {
        // echo sprintf(
        //     'SERVICE:package=%s, file=%s, desc=%s, index=%d',
        //     $file->getPackage(),
        //     $file->getName(),
        //     $descriptor->getName(),
        //     $index
        // ) . PHP_EOL;
        foreach ($descriptor->getMethod() as $i => $method) {
            $this->wrapServiceMethod($method, $file, $i, $descriptor);
        }
    }

    protected function wrapServiceMethod(
        MethodDescriptorProto $method,
        FileDescriptorProto $file,
        $index,
        ServiceDescriptorProto $service
    ) {
        // echo sprintf(
        //     'METHOD:package=%s, file=%s, service=%s, method=%s, index=%d'
        //     . ', input=%s, output=%s',
        //     $file->getPackage(),
        //     $file->getName(),
        //     $service->getName(),
        //     $method->getName(),
        //     $index,
        //     $method->getInputType(),
        //     $method->getOutputType()
        // ) . PHP_EOL;
    }

    protected function wrapEnumDescriptor(
        EnumDescriptorProto $descriptor,
        FileDescriptorProto $file,
        $index
    ) {

        $fileName = sprintf(
            '%s/%s',
            trim(str_replace('.', '/', $file->getPackage()), '/'),
            $descriptor->getName(). '.java'
        );
        // echo sprintf(
        //     'ENUM:package=%s, file=%s, desc=%s, index=%d, fileName=%s',
        //     $file->getPackage(),
        //     $file->getName(),
        //     $descriptor->getName(),
        //     $index,
        //     $fileName
        // ) . PHP_EOL;

        $genEnum = new GenEnum($descriptor, $file);
        $genEnum->generate();
        $genFile = new ResponseFile;
        $genFile->setName($fileName);
        $genFile->setContent($genEnum->getContent());
        $this->appendResponseFile($genFile);
    }

    protected function wrapDescriptor(
        DescriptorProto $descriptor,
        FileDescriptorProto $file,
        $index
    ) {

        $fileName = sprintf(
            '%s/%s',
            trim(str_replace('.', '/', $file->getPackage()), '/'),
            $descriptor->getName(). '.java'
        );

        // echo sprintf(
        //     'MESSAGE:package=%s, file=%s, desc=%s, index=%d, fileName=%s',
        //     $file->getPackage(),
        //     $file->getName(),
        //     $descriptor->getName(),
        //     $index,
        //     $fileName
        // ) . PHP_EOL;

        foreach ($descriptor->getNestedType() as $i => $nestedDescriptor) {
            throw new RuntimeException('please remove NestedType, not support!');
            $this->wrapDescriptor($nestedDescriptor, $file, $i, $descriptorStruct);
        }
        foreach ($descriptor->getEnumType() as $i => $enumDescriptor) {
            throw new RuntimeException('please remove EnumType, not support!');
            $this->wrapEnumDescriptor($enumDescriptor, $file, $i, $descriptorStruct);
        }
        $genMessage = new GenMessage($descriptor, $file);
        $genMessage->generate();

        $genFile = new ResponseFile;
        $genFile->setName($fileName);
        $genFile->setContent($genMessage->getContent());
        $this->appendResponseFile($genFile);
    }

    public function appendResponseFile(ResponseFile $file)
    {
        $this->response->getFile()[] = $file; 
    }
}