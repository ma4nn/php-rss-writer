<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

interface Xmlable
{
    public function asXML(): SimpleXMLElement;
}
