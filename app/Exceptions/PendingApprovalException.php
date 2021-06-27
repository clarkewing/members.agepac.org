<?php

namespace App\Exceptions;

use Exception;

class PendingApprovalException extends Exception
{
    /**
     * The path the user should be redirected to.
     *
     * @var string|null
     */
    protected ?string $redirectTo;

    /**
     * Create a new pending approval exception.
     *
     * @param  string  $message
     * @param  string|null  $redirectTo
     */
    public function __construct($message = 'User has not yet been approved.', string $redirectTo = null)
    {
        parent::__construct($message);

        $this->redirectTo = $redirectTo;
    }

    /**
     * Get the path the user should be redirected to.
     *
     * @return string|null
     */
    public function redirectTo(): ?string
    {
        return $this->redirectTo;
    }
}
