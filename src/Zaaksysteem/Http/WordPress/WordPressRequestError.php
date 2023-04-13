<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use WP_Error;
use OWC\Zaaksysteem\Http\RequestError;

class WordPressRequestError extends RequestError
{
    public static function fromWpError(WP_Error $error): self
    {
        return new self($error->get_error_message());
    }
}
