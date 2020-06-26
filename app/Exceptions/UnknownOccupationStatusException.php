<?php

namespace App\Exceptions;

use App\Occupation;
use Exception;
use Illuminate\Support\Facades\Log;

class UnknownOccupationStatusException extends Exception
{
    /**
     * The occupation with the unknown status.
     *
     * @var \App\Occupation
     */
    protected $occupation;

    /**
     * The unknown status.
     *
     * @var mixed
     */
    protected $status;

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  \App\Occupation  $occupation
     * @param  mixed  $status
     */
    public function __construct($message = 'Unknown Occupation status.', Occupation $occupation, $status = null)
    {
        parent::__construct($message);

        $this->occupation = $occupation;

        $this->status = $status ?? $occupation->getRawOriginal('status_code');
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        Log::alert('Unknown Occupation status.', [
            'occupation' => $this->occupation,
            'status' => $this->status,
        ]);
    }
}
