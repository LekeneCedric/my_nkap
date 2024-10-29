<?php

use App\Shared\Infrastructure\Enums\DiscordWebhookEnum;

return [
    /*
     * The webhook URLs that we'll use to send a message to Discord.
     */
    'webhook_urls' => [
        DiscordWebhookEnum::MEMBERS_NOTIFICATIONS => 'https://discord.com/api/webhooks/1300741657506877440/HfBs24pPoMOIUzdEF9nVZAClXn05jNKn_lWZsLVSCgAwDBtCp-e_Gw5yxaMXXMCM27Jn',
        DiscordWebhookEnum::ISSUES_NOTIFICATIONS => 'https://discord.com/api/webhooks/1300741756668743701/rhVVhG9gUKbptYS-hzq0aftH2vY0SxIZ6cbPGBKTCzFxlnNJqklVQ5iRlqvVAJN88p9x'
    ],

    /*
     * This job will send the message to Discord. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\DiscordAlerts\Jobs\SendToDiscordChannelJob::class,
];
