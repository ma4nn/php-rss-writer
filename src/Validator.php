<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

use DOMDocument;

final class Validator
{
    private DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
    }

    public function validate(string $xml): bool
    {
        $this->dom->loadXML($xml, LIBXML_NOBLANKS);

        return $this->dom->schemaValidate(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'rss-2.0.xsd');
    }

    /** @return array<int, \LibXMLError> */
    public function getLastErrors(): array
    {
        return libxml_get_errors();
    }
}