<?php

namespace Wechat\Response\XML;

class Element extends \SimpleXMLElement
{
    public function append($append)
    {
        if (!is_object($append)) {
            return;
        }

        if ($append->children()->count() > 0) {
            $xml = $this->addChild($append->getName());
            foreach ($append->children() as $child) {
                $xml->append($child);
            }
        } else {
            $xml = $this->addChild($append->getName(), (string) $append);
        }

        foreach ($append->attributes() as $n => $v) {
            $xml->addAttribute($n, $v);
        }
    }

    public function addChild($key, $value = null, $namespace = null)
    {
        if ($value instanceof CData) {
            $this->$key = null;
            $node = dom_import_simplexml($this->$key);
            $doc = $node->ownerDocument;
            $node->appendChild($doc->createCDATASection((string) $value));

            return $this;
        } else {
            return parent::addChild($key, $value, $namespace);
        }
    }
}
