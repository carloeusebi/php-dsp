<?php

namespace core;

class Session
{
    protected const FLASH = 'flash_messages';

    public function __construct()
    {
        session_start();
        # For CORS
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");

        // marks every flash message already existing so that they will be removed when Session dies
        $flashMessages = $_SESSION[self::FLASH] ??  [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH] = $flashMessages;
    }


    public function setFlash(string $key, string|array $message)
    {
        $_SESSION[self::FLASH][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash(string $key): string|array|null
    {
        return $_SESSION[self::FLASH][$key]['value'] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[self::FLASH][$key]);
    }


    public function __destruct()
    {
        $this->removeFlashMessages();
    }


    private function removeFlashMessages(): void
    {
        $flashMessages = $_SESSION[self::FLASH] ?? [];

        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH] = $flashMessages;
    }
}
