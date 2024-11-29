<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

class Channel implements ChannelInterface
{
    public function __construct(
        protected string|null $title = null,
        protected string|null $url = null,
        protected string|null $feedUrl = null,
        protected string|null $description = null,
        protected string|null $language = null,
        protected string|null $copyright = null,
        protected int|null $pubDate = null,
        protected int|null $lastBuildDate = null,
        protected int|null $ttl = null,

        /** @var string[] */
        protected array|null $pubsubhubbub = null,

        /** @var ItemInterface[] */
        protected array $items = []
    ) {}

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function feedUrl(string $url): self
    {
        $this->feedUrl = $url;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /** @inheritDoc */
    public function language(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function copyright(string $copyright): self
    {
        $this->copyright = $copyright;
        return $this;
    }

    public function pubDate(int $pubDate): self
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    public function lastBuildDate(int $lastBuildDate): self
    {
        $this->lastBuildDate = $lastBuildDate;
        return $this;
    }

    public function ttl(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    public function pubsubhubbub(string $feedUrl, string $hubUrl): self
    {
        $this->pubsubhubbub = [
            'feedUrl' => $feedUrl,
            'hubUrl' => $hubUrl,
        ];
        return $this;
    }

    public function addItem(ItemInterface $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function appendTo(FeedInterface $feed): self
    {
        $feed->addChannel($this);
        return $this;
    }

    /** @throws \Exception */
    public function asXML(): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel></channel>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);
        $xml->addChild('title', $this->title);
        $xml->addChild('link', $this->url);
        $xml->addCdataChild('description', $this->description);

        if ($this->feedUrl !== null) {
            $link = $xml->addChild('atom:link', '', "http://www.w3.org/2005/Atom");
            $link->addAttribute('href',$this->feedUrl);
            $link->addAttribute('type','application/rss+xml');
            $link->addAttribute('rel','self');
        }

        if ($this->language !== null) {
            $xml->addChild('language', $this->language);
        }

        if ($this->copyright !== null) {
            $xml->addChild('copyright', $this->copyright);
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
        }

        if ($this->lastBuildDate !== null) {
            $xml->addChild('lastBuildDate', date(DATE_RSS, $this->lastBuildDate));
        }

        if ($this->ttl !== null) {
            $xml->addChild('ttl', (string)$this->ttl);
        }

        if ($this->pubsubhubbub !== null) {
            $feedUrl = $xml->addChild('xmlns:atom:link');
            $feedUrl->addAttribute('rel', 'self');
            $feedUrl->addAttribute('href', $this->pubsubhubbub['feedUrl']);
            $feedUrl->addAttribute('type', 'application/rss+xml');

            $hubUrl = $xml->addChild('xmlns:atom:link');
            $hubUrl->addAttribute('rel', 'hub');
            $hubUrl->addAttribute('href', $this->pubsubhubbub['hubUrl']);
        }

        foreach ($this->items as $item) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($item->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}
