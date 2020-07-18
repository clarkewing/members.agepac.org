<?php

namespace Tests;

use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;

abstract class NovaTestCase extends TestCase
{
    /**
     * Send a POST request to store a Nova resource.
     *
     * @param  string  $resource
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeResource(string $resource, array $data = [])
    {
        return $this->postJson(
            action([ResourceStoreController::class, 'handle'], compact('resource')),
            $data
        );
    }

    /**
     * Send a PUT request to update a Nova resource.
     *
     * @param  string  $resource
     * @param  int|string  $resourceId
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateResource(string $resource, $resourceId, array $data = [])
    {
        return $this->putJson(
            action([ResourceUpdateController::class, 'handle'], compact('resource', 'resourceId')),
            $data
        );
    }
}
