<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

interface ItemInterface
{
    public function title(string $title): self;
    public function url(string $url): self;
    public function description(string $description): self;
    public function contentEncoded(string $content): self;
    public function category(string $name, string|null $domain = null): self;
    public function guid(string $guid, bool $isPermalink = false): self;
    public function pubDate(int $pubDate): self;
    public function enclosure(string $url, int $length = 0, string $type = 'audio/mpeg'): self;
    public function author(string $author): self;
    public function creator(string $creator): self;
    public function appendTo(ChannelInterface $channel): self;
    public function asXML(): SimpleXMLElement;
}
