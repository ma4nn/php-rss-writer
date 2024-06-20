<?php
declare(strict_types=1);

namespace Suin\RSSWriter;

interface FeedInterface
{
    public function addChannel(ChannelInterface $channel): self;

    public function render(): string;

    public function __toString(): string;
}
