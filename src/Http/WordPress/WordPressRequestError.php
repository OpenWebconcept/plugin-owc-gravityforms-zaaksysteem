<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use OWC\Zaaksysteem\Http\RequestError;
use WP_Error;

class WordPressRequestError extends RequestError
{
    public static function fromWpError(WP_Error $error): self
    {
        return new self($error->get_error_message());
    }
}
