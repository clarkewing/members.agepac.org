<?php

namespace App\Nova\Actions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

class MergeModels extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Merge';

    /**
     * The models' primary key.
     *
     * @var string
     */
    protected string $primaryKey;

    /**
     * The array of the relationships to be transferred on merge.
     *
     * @var array
     */
    protected array $relationships;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() < 2) {
            return Action::danger('At least two models must be selected to merge.');
        }

        $models = $models->keyBy($this->primaryKey = $models->first()->getKeyName());

        $preservedModel = $models->pull($fields->preserved_id);

        if (is_null($preservedModel)) {
            return Action::danger('The model ID to be preserved was not found in the merge selection.');
        }

        foreach ($this->relationships as $relationshipName) {
            $relationshipInstance = $preservedModel->$relationshipName();

            if ($relationshipInstance instanceof BelongsToMany) {
                $table = $relationshipInstance->getTable();
                $foreignPivotKey = $relationshipInstance->getForeignPivotKeyName();

                DB::table($table)
                    ->whereIn($foreignPivotKey, $models->pluck($this->primaryKey))
                    ->update([$foreignPivotKey => $preservedModel->{$this->primaryKey}]);
            }
        }

        $models->each->delete();

        return Action::message('Models merged successfully.');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make('Model ID to preserve', 'preserved_id')
                ->help('The ID of the model that should be preserved after the merge')
                ->rules(['required', 'integer'])
                ->withMeta([
                    'extraAttributes' => [
                        'inputmode' => 'numeric',
                        'pattern' => '[0-9]*',
                    ],
                ]),
        ];
    }

    /**
     * Set the relationships that should be transferred.
     *
     * @param  array  $relationships
     * @return $this
     */
    public function relationships(array $relationships = []): self
    {
        $this->relationships = $relationships;

        return $this;
    }
}
