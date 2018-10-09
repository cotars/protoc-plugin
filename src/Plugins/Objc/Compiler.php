<?php

namespace Cotars\Protoc\Plugins\Objc;

use Cotars\Protoc\Plugins\CompilerBase;
use Cotars\Protoc\Plugins\GenMessageBase;
use Cotars\Protoc\Plugins\Objc\GenMessageH;
use Google\Protobuf\Compiler\CodeGeneratorResponse_File as ResponseFile;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\EnumDescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use RuntimeException;

class Compiler extends CompilerBase
{
    protected $hContents = [];
    protected $mContents = [];
    protected $classLines = [];
    public function compile()
    {
        foreach ($this->parser->getRequest()->getProtoFile() as $file) {
            $this->compileFile($file);
        }

        $hFileName = 'proto';
        if ($this->classLines) {
            array_unshift($this->hContents, ...$this->classLines);
        }
        array_unshift($this->hContents, '#import <Foundation/Foundation.h>');
        $genFile = new ResponseFile;
        $genFile->setName($hFileName.'.h');
        $genFile->setContent(implode("\n", $this->hContents));
        $this->parser->appendCompiled($genFile);

        array_unshift($this->mContents, sprintf('#import "'.$hFileName.'.h"'));
        $genFile = new ResponseFile;
        $genFile->setName($hFileName.'.m');
        $genFile->setContent(implode("\n", $this->mContents));
        $this->parser->appendCompiled($genFile);
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
        $genEnumH = new GenEnumH($enum, $file);
        $genEnumH->setParser($this->parser);
        $genEnumH->generate();
        array_unshift($this->hContents, $genEnumH->getContent());

        $genEnumM = new GenEnumM($enum, $file);
        $genEnumM->setParser($this->parser);
        $genEnumM->generate();
        array_unshift($this->mContents, $genEnumM->getContent());

    }

    public function compileMessage(
        FileDescriptorProto $file,
        DescriptorProto $message,
        DescriptorProto ...$parents
    ) {

        $genMessageH = new GenMessageH($message, $file);
        $genMessageH->setParser($this->parser);
        $genMessageH->generate();
        $this->hContents[] = $genMessageH->getContent();
        if ($genMessageH->getClassLines()) {
            array_unshift($this->classLines, ...$genMessageH->getClassLines());
            $this->classLines = array_unique($this->classLines);
        }
        $genMessageM = new GenMessageM($message, $file);
        $genMessageM->setParser($this->parser);
        $genMessageM->generate();
        $this->mContents[] = $genMessageM->getContent();

        $parents[] = $message;
        foreach ($message->getNestedType() as $nested) {
            throw new RuntimeException('please remove NestedType, not support, ->'.$nested->getName());
        }

        foreach ($message->getEnumType() as $enumDescriptor) {
            throw new RuntimeException('please remove EnumType, not support! ->'.$enumDescriptor->getName());
        }
    }
}
