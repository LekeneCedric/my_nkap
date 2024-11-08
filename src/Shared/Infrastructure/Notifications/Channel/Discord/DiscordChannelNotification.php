<?php

namespace App\Shared\Infrastructure\Notifications\Channel\Discord;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\DiscordWebhookEnum;
use App\Shared\Infrastructure\Notifications\Channel\Discord\Factory\DiscordChannelNotificationContentFactory;
use Illuminate\Support\Facades\App;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class DiscordChannelNotification implements ChannelNotification
{

    public function send(ChannelNotificationContent $notification): void
    {
      $content = DiscordChannelNotificationContentFactory::buildFromNotification($notification);

      $inDeveloperMode = in_array(App::environment(), ['testing', 'local']);
      $webhook = $notification->type() === ChannelNotificationTypeEnum::ISSUE ?
          DiscordWebhookEnum::ISSUES_NOTIFICATIONS :
          DiscordWebhookEnum::MEMBERS_NOTIFICATIONS;
        if (true) {
            try {
                DiscordAlert::to($webhook)
                    ->message($content);
            } catch (\Exception $e) {
            }
        }
    }
}
