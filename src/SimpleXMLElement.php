<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

class SimpleXMLElement extends \SimpleXMLElement
{
    public function addChild(string $qualifiedName, string|null $value = null, string|null $namespace = null): ?static
    {
        if ($value !== null) {
            $value = str_replace('&', '&amp;', $value);
        }

        return parent::addChild($qualifiedName, $value, $namespace);
    }

    public function addCdataChild(string $name, string|null $value = null, string|null $namespace = null): self
    {
        $element = $this->addChild($name, null, $namespace);
        $dom = dom_import_simplexml($element);
        $elementOwner = $dom->ownerDocument;
        $dom->appendChild($elementOwner->createCDATASection($value));

        return $element;
    }
}
