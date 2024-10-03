<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use Aura\Session\Segment;
use OWC\Zaaksysteem\Contracts\BsnResolver;

use function Yard\DigiD\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDBsnResolver implements BsnResolver
{
    protected Segment $segment;

    final private function __construct()
    {
        $this->segment = resolve('session')->getSegment('digid');
    }

    public static function make(): self
    {
        return new static();
    }

    public function get(string $key)
    {
        return $this->segment->get($key) ?? null;
    }

    public function bsn(): string
    {
        $bsn = $this->get('bsn');

        /**
         * TEMP: signicat plugin has some changes pending which requires another implementation.
         */
        if (empty($bsn)) {
            $isLoggedIn = apply_filters('owc_siginicat_openid_is_user_logged_in', false, 'digid');

            if ($isLoggedIn) {
                $userInfo = apply_filters('owc_signicat_openid_user_info', [], 'digid');
                $bsn = $userInfo['sub'] ?? '';
            }

            return ! empty($bsn) && is_string($bsn) ? $bsn : '';
        }

        return ! empty($bsn) && is_string($bsn) ? decrypt($bsn) : '';
    }
}
