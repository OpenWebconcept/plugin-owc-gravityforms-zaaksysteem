<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use OWC\IdpUserData\eHerkenningSession;
use OWC\Zaaksysteem\Contracts\IdentificationResolver;

class eHerkenningResolver implements IdentificationResolver
{
    public static function make(): self
    {
        return new static();
    }

    public function get(): string
    {
        if (! eHerkenningSession::isLoggedIn()) {
            return '';
        }

        $userData = eHerkenningSession::getUserData();

        return ! is_null($userData) ? $userData->getKvk() : '';
    }
}
