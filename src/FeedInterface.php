<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

interface FeedInterface extends \Stringable
{
    public function addChannel(ChannelInterface $channel): self;

    public function render(): string;
}
