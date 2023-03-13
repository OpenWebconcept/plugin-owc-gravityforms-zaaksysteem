<?php

namespace OWC\OpenZaak\Foundation;

class Cryptor
{
    protected string $method = 'aes-128-ctr'; // default cipher method if none supplied
    private string $key;

    public function __construct(bool $method = false)
    {
        $key = \AUTH_KEY ?? php_uname();

        if (ctype_print($key)) {
            // convert ASCII keys to binary format
            $this->key = openssl_digest($key, 'SHA256', true);
        } else {
            $this->key = $key;
        }

        if ($method) {
            if (! in_array(strtolower($method), openssl_get_cipher_methods())) {
                throw new \Exception(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
            $this->method = $method;
        }
    }

    protected function ivBytes(): int
    {
        $cipher = openssl_cipher_iv_length($this->method);

        if (! $cipher || ! is_int($cipher)) {
            throw new \Exception('Failed to decrypt.');
        }

        return $cipher;
    }

    public function encrypt($data): string
    {
        $iv = openssl_random_pseudo_bytes($this->ivBytes());
        return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    }

    public function decrypt($data)
    {
        $ivStrlen = 2 * $this->ivBytes();

        if (preg_match("/^(.{" . $ivStrlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;

            if (ctype_xdigit($iv) && 0 == strlen($iv) % 2) {
                return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
            }
        }

        return false;
    }
}
