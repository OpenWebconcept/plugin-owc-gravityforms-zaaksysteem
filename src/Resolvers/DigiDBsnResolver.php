<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use OWC\IdpUserData\DigiDSession;
use OWC\Zaaksysteem\Contracts\IdentificationResolver;

class DigiDBsnResolver implements IdentificationResolver
{

    public static function make(): self
    {
        return new static();
    }

    public function get(): string
    {
        if (! DigiDSession::isLoggedIn()) {
            return '';
        }

        $userData = DigiDSession::getUserData();

        return ! is_null($userData) ? $userData->getBsn() : '';
    }
}
