<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use Closure;

trait Sortable
{
    public function sort(?Closure $callback = null, bool $reverse = false)
    {
        if ($callback) {
            usort($this->data, $callback);
            $this->data = $reverse ? array_reverse($this->data) : $this->data;

            return $this;
        }

        $reverse ? rsort($this->data) : sort($this->data);

        return $this;
    }

    public function asort(?Closure $callback = null, bool $reverse = false)
    {
        if ($callback) {
            uasort($this->data, $callback);
            $this->data = $reverse ? array_reverse($this->data) : $this->data;

            return $this;
        }

        $reverse ? arsort($this->data) : asort($this->data);

        return $this;
    }

    public function ksort(?Closure $callback = null, bool $reverse = false)
    {
        if ($callback) {
            uksort($this->data, $callback);
            $this->data = $reverse ? array_reverse($this->data) : $this->data;

            return $this;
        }

        $reverse ? krsort($this->data) : ksort($this->data);

        return $this;
    }

    public function sortByAttribute(string $attribute, bool $reverse = false)
    {
        return $this->sort(function ($themeA, $themeB) use ($attribute) {
            if ($themeA->$attribute == $themeB->$attribute) {
                return 0;
            }

            return ($themeA->$attribute < $themeB->$attribute) ? -1 : 1;
        }, $reverse);
    }
}
