<?php

namespace Cotars\Protoc\Plugins\JavaBean;

use Cotars\Protoc\Plugins\CompilerBase;
use Cotars\Protoc\Plugins\GenMessageBase;
use Google\Protobuf\Compiler\CodeGeneratorResponse_File as ResponseFile;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use RuntimeException;

class Compiler extends CompilerBase
{
    public function compile()
    {
        foreach ($this->parser->getRequest()->getProtoFile() as $file) {
            $this->compileFile($file);
        }
    }

    public function compileFile(FileDescriptorProto $file)
    {
        foreach ($file->getMessageType() as $message) {
            $this->compileMessage($file, $message);
        }
        foreach ($file->getEnumType() as $enum) {
            $this->compileEnum($file, $enum);
        }
    }

    protected function compileEnum(
        FileDescriptorProto $file,
        EnumDescriptorProto $enum
    ) {

        $fileName = sprintf(
            '%s/%s',
            trim(str_replace('.', '/', $file->getPackage()), '/'),
            $enum->getName(). '.java'
        );
        $genEnum = new GenEnum($enum, $file);
        $genEnum->setParser($this->parser);
        $genEnum->generate();
        $genFile = new ResponseFile;
        $genFile->setName($fileName);
        $genFile->setContent($genEnum->getContent());
        $this->parser->appendCompiled($genFile);
    }

    public function compileMessage(
        FileDescriptorProto $file,
        DescriptorProto $message,
        DescriptorProto ...$parents
    ) {
        $fileName = sprintf(
            '%s/%s',
            trim(str_replace('.', '/', $file->getPackage()), '/'),
            $message->getName(). '.java'
        );
        $genMessage = new GenMessage($message, $file);
        $genMessage->setParser($this->parser);
        $genMessage->generate();
        $genFile = new ResponseFile;
        $genFile->setName($fileName);
        $genFile->setContent($genMessage->getContent());
        $this->parser->appendCompiled($genFile);
        $parents[] = $message;
        foreach ($message->getNestedType() as $nested) {
            // $option = $nested->getOptions();
            // if ($option && $option->hasMapEntry()) {
            //     continue;
            // }
            throw new RuntimeException('please remove NestedType, not support, ->'.$nested->getName());
            // $this->compileMessage($file, $nested, ...$parents);
        }

        foreach ($message->getEnumType() as $enumDescriptor) {
            throw new RuntimeException('please remove EnumType, not support! ->'.$enumDescriptor->getName());
        }
    }
}
