<?php

namespace Tests;

use Laravel\Nova\Http\Controllers\ActionController;
use Laravel\Nova\Http\Controllers\ResourceDestroyController;
use Laravel\Nova\Http\Controllers\ResourceForceDeleteController;
use Laravel\Nova\Http\Controllers\ResourceIndexController;
use Laravel\Nova\Http\Controllers\ResourceRestoreController;
use Laravel\Nova\Http\Controllers\ResourceShowController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;

trait NovaTestRequests
{
    /**
     * Send a GET request to index a Nova resource.
     *
     * @param  string  $resource
     * @return \Illuminate\Testing\TestResponse
     */
    protected function indexResource(string $resource)
    {
        return $this->getJson(
            action([ResourceIndexController::class, 'handle'], compact('resource'))
        );
    }

    /**
     * Send a GET request to view a Nova resource.
     *
     * @param  string  $resource
     * @param  int|string  $resourceId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function showResource(string $resource, $resourceId)
    {
        return $this->getJson(
            action([ResourceShowController::class, 'handle'], compact('resource', 'resourceId'))
        );
    }

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

    /**
     * Send a DELETE request to delete a Nova resource.
     *
     * @param  string  $resource
     * @param  int|string  $resourceId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteResource(string $resource, $resourceId)
    {
        return $this->deleteJson(
            action([ResourceDestroyController::class, 'handle'], [
                'resource' => $resource,
                'resources[]' => $resourceId,
            ])
        );
    }

    /**
     * Send a PUT request to restore a soft deleted Nova resource.
     *
     * @param  string  $resource
     * @param  int|string  $resourceId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function restoreResource(string $resource, $resourceId)
    {
        return $this->putJson(
            action([ResourceRestoreController::class, 'handle'], [
                'resource' => $resource,
                'resources[]' => $resourceId,
            ])
        );
    }

    /**
     * Send a DELETE request to force delete a Nova resource.
     *
     * @param  string  $resource
     * @param  int|string  $resourceId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function forceDeleteResource(string $resource, $resourceId)
    {
        return $this->deleteJson(
            action([ResourceForceDeleteController::class, 'handle'], [
                'resource' => $resource,
                'resources[]' => $resourceId,
            ])
        );
    }

    /**
     * List the actions for the given resource.
     *
     * @param  string  $resource
     * @return \Illuminate\Testing\TestResponse
     */
    protected function listResourceActions(string $resource)
    {
        return $this->getJson(
            action([ActionController::class, 'index'], compact('resource'))
        );
    }

    /**
     * Perform an action on the specified resources.
     *
     * @param  string  $resource
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function performResourceAction(string $resource, string $action, array $data = [])
    {
        return $this->postJson(
            action([ActionController::class, 'store'], compact('resource', 'action')),
            $data
        );
    }
}
