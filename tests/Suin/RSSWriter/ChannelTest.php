<?php

namespace Suin\RSSWriter;

class ChannelTest extends \XoopsUnit\TestCase
{
    private string $itemInterface = '\Suin\RSSWriter\ItemInterface';
    private string $feedInterface = '\Suin\RSSWriter\FeedInterface';

    public function testTitle()
    {
        $title = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->title($title));
    }

    public function testUrl()
    {
        $url = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->url($url));
    }

    public function testFeedUrl()
    {
        $channel = new Channel();
        $this->assertSame($channel, $channel->feedUrl('http://example.com/feed.xml'));
        $feedUrlXml = '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="http://example.com/feed.xml" type="application/rss+xml" rel="self"/>';
        $this->assertStringContainsString($feedUrlXml, $channel->asXML()->asXML());
    }

    public function testDescription()
    {
        $description = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->description($description));
    }

    public function testLanguage()
    {
        $language = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->language($language));
    }

    public function testCopyright()
    {
        $copyright = uniqid();
        $channel = new Channel();
        $this->assertSame($channel, $channel->copyright($copyright));
    }

    public function testPubDate()
    {
        $pubDate = mt_rand(0, 9999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->pubDate($pubDate));
    }

    public function testLastBuildDate()
    {
        $lastBuildDate = mt_rand(0, 9999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->lastBuildDate($lastBuildDate));
    }

    public function testTtl()
    {
        $ttl = mt_rand(0, 99999999);
        $channel = new Channel();
        $this->assertSame($channel, $channel->ttl($ttl));
    }

    public function testPubsubhubbub()
    {
        $channel = new Channel();
        $channel->pubsubhubbub('http://example.com/feed.xml', 'http://pubsubhubbub.appspot.com');
        $xml = $channel->asXML()->asXML();
        $this->assertStringContainsString('<atom:link rel="self" href="http://example.com/feed.xml" type="application/rss+xml"/>', $xml);
        $this->assertStringContainsString('<atom:link rel="hub" href="http://pubsubhubbub.appspot.com"/>', $xml);
    }

    public function testAddItem()
    {
        $item = $this->createMock($this->itemInterface);
        $channel = new Channel();
        $this->assertSame($channel, $channel->addItem($item));
    }

    public function testAppendTo()
    {
        $channel = new Channel();
        $feed = $this->createMock($this->feedInterface);
        $feed->expects($this->once())->method('addChannel')->with($channel);
        $this->assertSame($channel, $channel->appendTo($feed));
    }

    /**
     * @param       $expect
     * @param array $data
     * @dataProvider dataForAsXML
     */
    public function testAsXML($expect, array $data)
    {
        $data = (object)$data;
        $channel = new Channel();

        foreach ($data as $key => $value) {
            $this->reveal($channel)->attr($key, $value);
        }

        $this->assertXmlStringEqualsXmlString($expect, $channel->asXML()->asXML());
    }

    public static function dataForAsXML()
    {
        $now = time();
        $nowString = date(DATE_RSS, $now);

        return [
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <language>en-us</language>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'language'    => 'en-us',
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <pubDate>{$nowString}</pubDate>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'pubDate'     => $now,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <lastBuildDate>{$nowString}</lastBuildDate>
                </channel>
                ",
                [
                    'title'         => "GoUpstate.com News Headlines",
                    'url'           => 'http://www.goupstate.com/',
                    'description'   => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'lastBuildDate' => $now,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <ttl>60</ttl>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'ttl'         => 60,
                ]
            ],
            [
                "
                <channel>
                    <title>GoUpstate.com News Headlines</title>
                    <link>http://www.goupstate.com/</link>
                    <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                    <copyright>Copyright 2002, Spartanburg Herald-Journal</copyright>
                </channel>
                ",
                [
                    'title'       => "GoUpstate.com News Headlines",
                    'url'         => 'http://www.goupstate.com/',
                    'description' => "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.",
                    'copyright'   => "Copyright 2002, Spartanburg Herald-Journal",
                ]
            ],
        ];
    }

    public function testAppendTo_with_items()
    {
        $channel = new Channel();

        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item1</title></item>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item2</title></item>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item><title>item3</title></item>');

        $item1 = $this->createMock($this->itemInterface);
        $item1->expects($this->once())->method('asXML')->will($this->returnValue($xml1));
        $item2 = $this->createMock($this->itemInterface);
        $item2->expects($this->once())->method('asXML')->will($this->returnValue($xml2));
        $item3 = $this->createMock($this->itemInterface);
        $item3->expects($this->once())->method('asXML')->will($this->returnValue($xml3));

        $this->reveal($channel)
            ->attr('title', "GoUpstate.com News Headlines")
            ->attr('url', 'http://www.goupstate.com/')
            ->attr('description', "The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.")
            ->attr('items', [$item1, $item2, $item3]);

        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <channel>
                <title>GoUpstate.com News Headlines</title>
                <link>http://www.goupstate.com/</link>
                <description>The latest news from GoUpstate.com, a Spartanburg Herald-Journal Web site.</description>
                <item>
                    <title>item1</title>
                </item>
                <item>
                    <title>item2</title>
                </item>
                <item>
                    <title>item3</title>
                </item>
            </channel>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $channel->asXML()->asXML());
    }
}
