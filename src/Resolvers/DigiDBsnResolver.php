<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use OWC\IdpUserData\DigiDSession;
use OWC\Zaaksysteem\Contracts\BsnResolver;

class DigiDBsnResolver implements BsnResolver
{

    public static function make(): self
    {
        return new static();
    }

    public function bsn(): string
    {
        if(!DigiDSession::isLoggedIn()) {
            return '';
        }
        
        return DigiDSession::getUserData()->getBsn();
    }
}
