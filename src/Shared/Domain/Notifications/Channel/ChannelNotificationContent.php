<?php

namespace App\Shared\Domain\Notifications\Channel;

class ChannelNotificationContent
{
    private string $occurredOn;
    public function __construct(
        private ChannelNotificationTypeEnum $type,
        private array $data
    )
    {
        $this->occurredOn = (new \DateTimeImmutable())->format("d-m-Y H-i-s");
    }

    /**
     * @return ChannelNotificationTypeEnum
     */
    public function type(): ChannelNotificationTypeEnum
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function occuredOn(): string
    {
        return $this->occurredOn;
    }
}
