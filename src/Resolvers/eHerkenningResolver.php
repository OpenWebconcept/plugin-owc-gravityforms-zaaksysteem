<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use Exception;
use OWC\Zaaksysteem\Contracts\IdentificationResolver;

class eHerkenningResolver implements IdentificationResolver
{
    public static function make(): self
    {
        return new static();
    }

    public function get(): string
    {
        if ($kvk = $this->handle_kvk_idp()) {
            return $kvk;
        }

        if ($kvk = $this->handle_kvk_saml()) {
            return $kvk;
        }

        return '';
    }

    private function handle_kvk_idp(): string
    {
        if (! class_exists('\OWC\IdpUserData\eHerkenningSession')) {
            return '';
        }

        $user = \OWC\IdpUserData\eHerkenningSession::getUserData();

        if (! \OWC\IdpUserData\eHerkenningSession::isLoggedIn() || null === $user) {
            return '';
        }

        return $user->getKvk();
    }

    private function handle_kvk_saml(): string
    {
        if (! function_exists('\\Yard\\eHerkenning\\Foundation\\Helpers\\resolve')) {
            return '';
        }

        try {
            $kvk = \Yard\eHerkenning\Foundation\Helpers\resolve('session')->getSegment('eherkenning')->get('kvk');
        } catch (Exception $e) {
            $kvk = '';
        }

        return is_string($kvk) && ! empty($kvk) ? $kvk : '';
    }
}
