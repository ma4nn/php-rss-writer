<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

interface ChannelInterface
{
    public function title(string $title): self;
    public function url(string $url): self;
    public function description(string $description): self;

    /**
     * Set ISO639 language code
     *
     * The language the channel is written in. This allows aggregators to group all
     * Italian language sites, for example, on a single page. A list of allowable
     * values for this element, as provided by Netscape, is here. You may also use
     * values defined by the W3C.
     *
     * @param string $language
     * @return $this
     */
    public function language(string $language): self;

    public function copyright(string $copyright): self;
    public function pubDate(int $pubDate): self;
    public function lastBuildDate(int $lastBuildDate): self;
    public function ttl(int $ttl): self;
    public function addItem(ItemInterface $item): self;
    public function appendTo(FeedInterface $feed): self;
    public function asXML(): SimpleXMLElement;
}
