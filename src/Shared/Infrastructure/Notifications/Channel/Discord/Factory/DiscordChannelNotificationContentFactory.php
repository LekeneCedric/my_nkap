<?php

namespace App\Shared\Infrastructure\Notifications\Channel\Discord\Factory;

use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;

class DiscordChannelNotificationContentFactory
{
    /**
     * @param ChannelNotificationContent $notification
     * @return string
     */
    public static function buildFromNotification(ChannelNotificationContent $notification): string
    {
        $data = $notification->data();
        if ($notification->type() === ChannelNotificationTypeEnum::NEW_MEMBER) {
            return ':white_check_mark: **Title:** New User Added' .
                "\n**info:** " . $data['users_data'].
                "\n**Total Users ** " . $data['total_users'];
        }

        $icon = match ($data['level']) {
            ErrorLevelEnum::INFO->value => ':information_source:',
            ErrorLevelEnum::WARNING->value => ':warning:',
            default => ':japanese_goblin:'
        };
        return $icon . ' **Title:** Issue Detected' .
            "\n**Module:** " . $data['module'].
            "\n**Occurred At:** " . $notification->occuredOn().
            "\n**Message:** " . $data['message'] .
            "\n**Commande:** ``` " . $data['command'] . "```".
            "\n**Exception Trace:** ```" . $data['trace'] . "```";
    }
}
