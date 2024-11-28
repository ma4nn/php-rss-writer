# RSS Writer Library for PHP

![build status](https://github.com/ma4nn/php-rss-writer/actions/workflows/ci.yml/badge.svg)

`\Suin\RSSWriter` is yet another simple RSS writer library for PHP. This component is Licensed under MIT license.

This library can also be used to publish Podcasts.

This fork of [the original package](https://github.com/suin/php-rss-writer) mainly raises the PHP compatibility level to >= 8.2 and improves code quality.

## Quick demo

```php
$feed = new Feed();

$channel = new Channel();
$channel
    ->title('Channel Title')
    ->description('Channel Description')
    ->url('http://blog.example.com')
    ->feedUrl('http://blog.example.com/rss')
    ->language('en-US')
    ->copyright('Copyright 2012, Foo Bar')
    ->pubDate(strtotime('Tue, 21 Aug 2012 19:50:37 +0900'))
    ->lastBuildDate(strtotime('Tue, 21 Aug 2012 19:50:37 +0900'))
    ->ttl(60)
    ->pubsubhubbub('http://example.com/feed.xml', 'http://pubsubhubbub.appspot.com') // This is optional. Specify PubSubHubbub discovery if you want.
    ->appendTo($feed);

// Blog item
$item = new Item();
$item
    ->title('Blog Entry Title')
    ->description('<div>Blog body</div>')
    ->contentEncoded('<div>Blog body</div>')
    ->url('http://blog.example.com/2012/08/21/blog-entry/')
    ->author('john@smith.com')
    ->creator('John Smith')
    ->pubDate(strtotime('Tue, 21 Aug 2012 19:50:37 +0900'))
    ->guid('http://blog.example.com/2012/08/21/blog-entry/', true)
    ->preferCdata(true) // By this, title and description become CDATA wrapped HTML.
    ->appendTo($channel);

// Podcast item
$item = new Item();
$item
    ->title('Some Podcast Entry')
    ->description('<div>Podcast body</div>')
    ->url('http://podcast.example.com/2012/08/21/podcast-entry/')
    ->enclosure('http://podcast.example.com/2012/08/21/podcast.mp3', 4889, 'audio/mpeg')
    ->appendTo($channel);

echo $feed; // or echo $feed->render();
```

Output:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
  <channel>
    <title>Channel Title</title>
    <link>http://blog.example.com</link>
    <description>Channel Description</description>
    <language>en-US</language>
    <copyright>Copyright 2012, Foo Bar</copyright>
    <pubDate>Tue, 21 Aug 2012 10:50:37 +0000</pubDate>
    <lastBuildDate>Tue, 21 Aug 2012 10:50:37 +0000</lastBuildDate>
    <ttl>60</ttl>
    <atom:link rel="self" href="http://example.com/feed.xml" type="application/rss+xml"/>
    <atom:link rel="hub" href="http://pubsubhubbub.appspot.com"/>
    <item>
      <title><![CDATA[Blog Entry Title]]></title>
      <link>http://blog.example.com/2012/08/21/blog-entry/</link>
      <description><![CDATA[<div>Blog body</div>]]></description>
      <content:encoded><![CDATA[<div>Blog body</div>]]></content:encoded>
      <guid>http://blog.example.com/2012/08/21/blog-entry/</guid>
      <pubDate>Tue, 21 Aug 2012 10:50:37 +0000</pubDate>
      <author>john@smith.com</author>
      <creator>John Smith</creator>
    </item>
    <item>
      <title>Some Podcast Entry</title>
      <link>http://podcast.example.com/2012/08/21/podcast-entry/</link>
      <description>&lt;div&gt;Podcast body&lt;/div&gt;</description>
      <enclosure url="http://podcast.example.com/2012/08/21/podcast.mp3" type="audio/mpeg" length="4889"/>
    </item>
  </channel>
</rss>
```

## Installation

### Easy installation

You can install directly via [composer](https://getcomposer.org/):

```bash
$ composer require ma4nn/php-rss-writer
```

## How to use

The [`examples`](examples) directory contains usage examples for RSSWriter.

If you want to know APIs, please see [`FeedInterface`](src/FeedInterface.php), [`ChannelInterface`](src/ChannelInterface.php) and [`ItemInterface`](src/ItemInterface.php).

## How to Test

```sh
$ vendor/bin/phpunit
```

## Test older PHP versions with Docker

```console
$ docker-compose up
```

## License

MIT license
