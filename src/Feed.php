<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

use DOMDocument;

class Feed implements FeedInterface
{
    /** @var ChannelInterface[] */
    protected array $channels = [];

    public function addChannel(ChannelInterface $channel): self
    {
        $this->channels[] = $channel;
        return $this;
    }

    public function render(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" />', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        foreach ($this->channels as $channel) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($channel->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode(dom_import_simplexml($xml), true));
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
