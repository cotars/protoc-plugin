<?php

namespace Cotars\Protoc\Plugins\JavaBean;

trait JavaTrait
{
    public function getContent(): string
    {
        $content = '';
        foreach ($this->content as $line) {
            list($code, $tab) = $line;
             $content .= str_pad('', $tab * 4, ' ').$code . "\n";
        }
        return $content;
    }
}