<?php

namespace App\Inspections;

class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class,
    ];
    /**
     * Detect whether body is spam.
     *
     * @param  string $body
     * @return bool
     */
    public function detect(string $body): bool
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }

        return false;
    }
}
