<?php
declare(strict_types=1);

namespace Suin\RSSWriter\Test;

use PHPUnit\Framework\TestCase;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\SimpleXMLElement;

final class FeedTest extends TestCase
{
    private string $channelInterface = \Suin\RSSWriter\ChannelInterface::class;

    public function testAddChannel(): void
    {
        $channel = $this->createMock($this->channelInterface);
        $feed = new Feed();
        $this->assertSame($feed, $feed->addChannel($channel));
    }

    public function testRender(): void
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel3</title></channel>');

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel1 */
        $channel1 = $this->createMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->willReturn($xml1);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel2 */
        $channel2 = $this->createMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->willReturn($xml2);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel3 */
        $channel3 = $this->createMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->willReturn($xml3);
        $feed->addChannel($channel1)
            ->addChannel($channel2)
            ->addChannel($channel3);
        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
                <channel><title>channel1</title></channel>
                <channel><title>channel2</title></channel>
                <channel><title>channel3</title></channel>
            </rss>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $feed->render());
    }

    public function testRender_with_japanese(): void
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>日本語3</title></channel>');

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel1 */
        $channel1 = $this->createMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->willReturn($xml1);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel2 */
        $channel2 = $this->createMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->willReturn($xml2);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel3 */
        $channel3 = $this->createMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->willReturn($xml3);
        $feed->addChannel($channel1)
            ->addChannel($channel2)
            ->addChannel($channel3);

        $expect = <<< 'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
  <channel>
    <title>日本語1</title>
  </channel>
  <channel>
    <title>日本語2</title>
  </channel>
  <channel>
    <title>日本語3</title>
  </channel>
</rss>

XML;
        $this->assertSame($expect, $feed->render());

    }

    public function test__toString(): void
    {
        $feed = new Feed();
        $xml1 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel1</title></channel>');
        $xml2 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel2</title></channel>');
        $xml3 = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel><title>channel3</title></channel>');

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel1 */
        $channel1 = $this->createMock($this->channelInterface);
        $channel1->expects($this->once())->method('asXML')->willReturn($xml1);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel2 */
        $channel2 = $this->createMock($this->channelInterface);
        $channel2->expects($this->once())->method('asXML')->willReturn($xml2);

        /** @var \Suin\RSSWriter\ChannelInterface&\PHPUnit\Framework\MockObject\MockObject $channel3 */
        $channel3 = $this->createMock($this->channelInterface);
        $channel3->expects($this->once())->method('asXML')->willReturn($xml3);
        $feed->addChannel($channel1)
            ->addChannel($channel2)
            ->addChannel($channel3);

        $expect = '<?xml version="1.0" encoding="UTF-8" ?>
            <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
                <channel><title>channel1</title></channel>
                <channel><title>channel2</title></channel>
                <channel><title>channel3</title></channel>
            </rss>
        ';
        $this->assertXmlStringEqualsXmlString($expect, strval($feed));
    }
}
