<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

class Item implements ItemInterface
{
    public function __construct(
        protected string|null $title = null,
        protected string|null $url = null,
        protected string|null $description = null,
        protected string|null $contentEncoded = null,

        /** @var list<string> */
        protected array $categories = [],

        protected string|null $guid = null,
        protected bool $isPermalink = false,
        protected int|null $pubDate = null,

        /** @var list<string> */
        protected array $enclosure = [],

        protected string|null $author = null,
        protected string|null $creator = null,
        protected bool $preferCdata = false
    )
    {
    }

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

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function contentEncoded(string $content): self
    {
        $this->contentEncoded = $content;
        return $this;
    }

    public function category(string $name, string|null $domain = null): self
    {
        $this->categories[] = [$name, $domain];
        return $this;
    }

    public function categories(array $categories) 
    {
        foreach ($categories as $cat) {
            $domain = null;
            if (is_array($cat) && !empty($cat)) {
                $domain = $cat[1] ?? null;
                $cat = $cat[0];
            }
            $this->category($cat, $domain);
        }
        return $this;
    }

    public function guid(string $guid, bool $isPermalink = false): self
    {
        $this->guid = $guid;
        $this->isPermalink = $isPermalink;
        return $this;
    }

    public function pubDate(int $pubDate): self
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    public function enclosure(string $url, int $length = 0, string $type = 'audio/mpeg'): self
    {
        $this->enclosure = ['url' => $url, 'length' => $length, 'type' => $type];
        return $this;
    }

    public function author(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function creator(string $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    public function preferCdata(bool $preferCdata): self
    {
        $this->preferCdata = $preferCdata;
        return $this;
    }

    public function appendTo(ChannelInterface $channel): self
    {
        $channel->addItem($this);
        return $this;
    }

    public function asXML(): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        if ($this->title) {
            if ($this->preferCdata) {
                $xml->addCdataChild('title', $this->title);
            } else {
                $xml->addChild('title', $this->title);
            }
        }

        if ($this->url) {
            $xml->addChild('link', $this->url);
        }

        // At least one of <title> or <description> must be present
        if ($this->description || ! $this->title) {
            if ($this->preferCdata) {
                $xml->addCdataChild('description', $this->description);
            } else {
                $xml->addChild('description', $this->description);
            }
        }

        if ($this->contentEncoded) {
            $xml->addCdataChild('xmlns:content:encoded', $this->contentEncoded);
        }

        foreach ($this->categories as $category) {
            $element = $xml->addChild('category', $category[0]);

            if (isset($category[1])) {
                $element->addAttribute('domain', $category[1]);
            }
        }

        if ($this->guid) {
            $guid = $xml->addChild('guid', $this->guid);

            if ($this->isPermalink === false) {
                $guid->addAttribute('isPermaLink', 'false');
            }
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));
        }

        if (is_array($this->enclosure) && (count($this->enclosure) == 3)) {
            $element = $xml->addChild('enclosure');
            $element->addAttribute('url', $this->enclosure['url']);
            $element->addAttribute('type', $this->enclosure['type']);
            $element->addAttribute('length', (string)($this->enclosure['length'] ?? 0));
        }

        if (! empty($this->author)) {
            $xml->addChild('author', $this->author);
        }

        if (! empty($this->creator)) {
            $xml->addChild('dc:creator', $this->creator,"http://purl.org/dc/elements/1.1/");
        }

        return $xml;
    }
}
