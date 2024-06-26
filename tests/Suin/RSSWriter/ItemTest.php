<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    private string $channelInterface = \Suin\RSSWriter\ChannelInterface::class;

    public function testTitle()
    {
        $title = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->title($title));
    }

    public function testUrl()
    {
        $url = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->url($url));
    }

    public function testDescription()
    {
        $description = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->description($description));
    }

    public function testContentEncoded()
    {
        $item = new Item();
        $this->assertSame($item, $item->contentEncoded('<div>contents</div>'));

        $feed = new Feed();
        $channel = new Channel();
        $item->appendTo($channel);
        $channel->appendTo($feed);

        $expected = '<?xml version="1.0" encoding="UTF-8"?>
        <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
          <channel>
            <title/>
            <link/>
            <description/>
            <item>
              <description/>
              <content:encoded><![CDATA[<div>contents</div>]]></content:encoded>
            </item>
          </channel>
        </rss>';
        $this->assertXmlStringEqualsXmlString($expected, $feed->render());
    }

    public function testCategory()
    {
        $category = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->category($category));
    }

    public function testCategory_with_domain()
    {
        $category = uniqid();
        $domain = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->category($category, $domain));
    }

    public function testCategories()
    {
        $categories = ['a', 'b', ['c', 'domain'], 'd', ['e']];
        $stored_categories = [
            ['a', null],
            ['b', null],
            ['c', 'domain'],
            ['d', null],
            ['e', null],
        ];
        $item = new Item();
        $item->categories($categories);
    }

    public function testGuid()
    {
        $guid = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->guid($guid));
    }

    public function testGuid_with_permalink()
    {
        $item = new Item();
        $item->guid('guid', true);

        $item->guid('guid', false);

        $item->guid('guid'); // default
    }

    public function testPubDate()
    {
        $pubDate = mt_rand(1000000, 9999999);
        $item = new Item();
        $this->assertSame($item, $item->pubDate($pubDate));
    }

    public function testAppendTo()
    {
        $item = new Item();
        $channel = $this->createMock($this->channelInterface);
        $channel->expects($this->once())->method('addItem')->with($item);
        $this->assertSame($item, $item->appendTo($channel));
    }

    public function testEnclosure()
    {
        $url = uniqid();
        $enclosure = ['url' => $url, 'length' => 0, 'type' => 'audio/mpeg'];
        $item = new Item();
        $this->assertSame($item, $item->enclosure($url));
    }

    public function testAuthor()
    {
        $author = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->author($author));
    }

    public function testCreator()
    {
        $creator = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->creator($creator));

        $creatorXml = '<dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">' . $creator . '</dc:creator>';
        $this->assertStringContainsString($creatorXml, $item->asXML()->asXML());
    }

    public function testPreferCdata()
    {
        $item = new Item();
        $item->title('<h1>title</h1>');
        $item->description('<p>description</p>');

        // By default, prefer no CDATA on title and description
        $actualXml = $item->asXML()->asXML();
        $this->assertStringContainsString('<title>&lt;h1&gt;title&lt;/h1&gt;</title>', $actualXml);
        $this->assertStringContainsString('<description>&lt;p&gt;description&lt;/p&gt;</description>', $actualXml);

        // Once prefer-cdata is enabled, title and description is wrapped by CDATA
        $item->preferCdata(true);
        $actualXml = $item->asXML()->asXML();
        $this->assertStringContainsString('<title><![CDATA[<h1>title</h1>]]></title>', $actualXml);
        $this->assertStringContainsString('<description><![CDATA[<p>description</p>]]></description>', $actualXml);

        // Of course, prefer-cdata can be disabled again
        $item->preferCdata(false);
        $actualXml = $item->asXML()->asXML();
        $this->assertStringContainsString('<title>&lt;h1&gt;title&lt;/h1&gt;</title>', $actualXml);
        $this->assertStringContainsString('<description>&lt;p&gt;description&lt;/p&gt;</description>', $actualXml);

        // And like other APIs `preferCdata` is also fluent interface
        $obj = $item->preferCdata(true);
        $this->assertSame($obj, $item);
    }

    public function testAsXML()
    {
        $now = time();
        $nowString = date(DATE_RSS, $now);

        $data = [
            'title'       => "Venice Film Festival Tries to Quit Sinking",
            'url'         => 'http://nytimes.com/2004/12/07FEST.html',
            'description' => "Some of the most heated chatter at the Venice Film Festival this week was about the way that the arrival of the stars at the Palazzo del Cinema was being staged.",
            'categories'  => [
                ["Grateful Dead", null],
                ["MSFT", 'http://www.fool.com/cusips'],
            ],
            'guid'        => "http://inessential.com/2002/09/01.php#a2",
            'isPermalink' => true,
            'pubDate'     => $now,
            'enclosure'   => [
                'url'    => 'http://link-to-audio-file.com/test.mp3',
                'length' => 4992,
                'type'   => 'audio/mpeg'
            ],
            'author'      => 'John Smith'
        ];

        $item = new Item(...$data);

        $expect = "
        <item>
            <title>{$data['title']}</title>
            <link>{$data['url']}</link>
            <description>{$data['description']}</description>
            <category>{$data['categories'][0][0]}</category>
            <category domain=\"{$data['categories'][1][1]}\">{$data['categories'][1][0]}</category>
            <guid>{$data['guid']}</guid>
            <pubDate>{$nowString}</pubDate>
            <enclosure url=\"{$data['enclosure']['url']}\" type=\"{$data['enclosure']['type']}\" length=\"{$data['enclosure']['length']}\"/>
            <author>{$data['author']}</author>
        </item>
        ";
        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    public function testAsXML_false_permalink()
    {
        $now = time();
        $nowString = date(DATE_RSS, $now);

        $data = [
            'title'       => "Venice Film Festival Tries to Quit Sinking",
            'url'         => 'http://nytimes.com/2004/12/07FEST.html',
            'description' => "Some of the most heated chatter at the Venice Film Festival this week was about the way that the arrival of the stars at the Palazzo del Cinema was being staged.",
            'categories'  => [
                ["Grateful Dead", null],
                ["MSFT", 'http://www.fool.com/cusips'],
            ],
            'guid'        => "http://inessential.com/2002/09/01.php#a2",
            'isPermalink' => false,
            'pubDate'     => $now,
            'enclosure'   => [
                'url'    => 'http://link-to-audio-file.com/test.mp3',
                'length' => 4992,
                'type'   => 'audio/mpeg'
            ],
            'author'      => 'John Smith'
        ];

        $item = new Item(...$data);

        $expect = "
        <item>
            <title>{$data['title']}</title>
            <link>{$data['url']}</link>
            <description>{$data['description']}</description>
            <category>{$data['categories'][0][0]}</category>
            <category domain=\"{$data['categories'][1][1]}\">{$data['categories'][1][0]}</category>
            <guid isPermaLink=\"false\">{$data['guid']}</guid>
            <pubDate>{$nowString}</pubDate>
            <enclosure url=\"{$data['enclosure']['url']}\" type=\"{$data['enclosure']['type']}\" length=\"{$data['enclosure']['length']}\"/>
            <author>{$data['author']}</author>
        </item>
        ";
        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    public function testAsXML_test_Japanese()
    {
        $data = [
            'title'       => "Venice Film Festival",
            'url'         => 'http://nytimes.com/2004/12/07FEST.html',
            'description' => "Some of the most heated chatter at the Venice Film Festival this week was about the way that the arrival of the stars at the Palazzo del Cinema was being staged.",
        ];

        $item = new Item(...$data);

        $expect = "
        <item>
            <title>{$data['title']}</title>
            <link>{$data['url']}</link>
            <description>{$data['description']}</description>
        </item>
        ";

        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    public function test_with_amp()
    {
        $item = new Item();
        $item
            ->title('test&test')
            ->url('url&url')
            ->description('desc&desc');
        $expect = '<?xml version="1.0" encoding="UTF-8"?>
<item><title>test&amp;test</title><link>url&amp;url</link><description>desc&amp;desc</description></item>
';

        $this->assertSame($expect, $item->asXML()->asXML());
    }

    public function test_fail_safe_against_invalid_string()
    {
        $item = new Item();
        $item
            ->title("test\0test")
            ->url("url\0test")
            ->description("desc\0desc");
        $expect = '<?xml version="1.0" encoding="UTF-8"?>
<item><title>test</title><link>url</link><description>desc</description></item>
';

        $this->assertSame($expect, $item->asXML()->asXML());
    }
}
