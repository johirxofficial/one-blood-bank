<?php

namespace App\Models\Concerns;

trait HasLocalizedName
{
    public function localizedName(): string
    {
        $english = app()->getLocale() === 'en';

        return (string) ($english
            ? ($this->name ?: $this->name_bn)
            : ($this->name_bn ?: $this->name));
    }
}
