<?php
declare(strict_types=1);

namespace Suin\RSSWriter\Test;

use PHPUnit\Framework\TestCase;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;
use Suin\RSSWriter\Validator;

final class OutputTest extends TestCase
{
    public function testSimpleFeed(): void
    {
        $feed = new Feed();
        $validator = new Validator();

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

        $feed = $feed->render();

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/simple-feed.xml', $feed);

        $isValid = $validator->validate($feed);
        var_dump($validator->getLastErrors());
        $this->assertEmpty($validator->getLastErrors());
        $this->assertTrue($isValid);
    }
}