<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown
{
    /**
     * Run the detection.
     *
     * @param  string $body
     * @throws \Exception
     * @return void
     */
    public function detect(string $body)
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new Exception('Your reply contains spam.');
        }
    }
}
