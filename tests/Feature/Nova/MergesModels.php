<?php

namespace Tests\Feature\Nova;

trait MergesModels
{
    /**
     * @param  string  $resource
     * @param  array  $resourceIds
     * @param  int|null  $preservedId
     * @return \Illuminate\Testing\TestResponse
     */
    protected function performMerge(string $resource, array $resourceIds = [], int $preservedId = null): \Illuminate\Testing\TestResponse
    {
        return $this->performResourceAction($resource, 'merge', [
            'resources' => implode(',', $resourceIds),
            'preserved_id' => $preservedId ?? $resourceIds[0],
        ]);
    }
}
