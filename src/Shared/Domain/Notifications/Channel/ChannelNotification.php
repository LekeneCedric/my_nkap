<?php

namespace App\Shared\Domain\Notifications\Channel;

interface ChannelNotification
{
    /**
     * @param ChannelNotificationContent $notification
     * @return void
     */
    public function send(ChannelNotificationContent $notification): void;
}
