<?php

namespace App\Shared\Infrastructure\Logs;

use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\Shared\Infrastructure\Logs\Model\LogMessage;

abstract class ModuleLogger
{
    public string $message;
    public LogLevelEnum $level;
    public string $description;

    public function Log(
        string       $message,
        LogLevelEnum $level,
        mixed        $description,
    ) {
        $this->message = $message;
        $this->level = $level;
        $this->description = $this->buildDescription($description);

        $this->dispatch();
    }

    private function buildDescription(mixed $description): string
    {
        return json_encode($description) ?: $this->message;
    }

    private function dispatch(): void
    {
        LogMessage::create([
            'message' => $this->message,
            'level' => $this->level,
            'description' => $this->description,
        ]);
    }
}
