<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords
{
    protected $keywords = [
        'Yahoo Customer Support',
    ];

    /**
     * Run the detection.
     *
     * @param  string $body
     * @throws \Exception
     * @return void
     */
    public function detect(string $body)
    {
        foreach ($this->keywords as $keywords) {
            if (stripos($body, $keywords) !== false) {
                throw new Exception('Your post contains spam.');
            }
        }
    }
}
