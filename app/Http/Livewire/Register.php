<?php

namespace App\Http\Livewire;

use Illuminate\Support\Arr;
use Livewire\Component;

class Register extends Component
{
    use Steps\Identity;
    use Steps\Credentials;
    use Steps\Details;
    use Steps\Summary;

    /**
     * Index of the currently active step.
     *
     * @var int
     */
    public $active = 0;

    /**
     * List of the steps.
     *
     * @var array
     */
    public $steps = [
        ['name' => 'Identity', 'completed' => false],
        ['name' => 'Credentials', 'completed' => false],
        ['name' => 'Details', 'completed' => false],
        ['name' => 'Summary', 'completed' => false],
        ['name' => 'Success', 'completed' => false],
    ];

    public function next(): void
    {
        $this->goToStep($this->active + 1);
    }

    public function goToStep($step): void
    {
        $stepNames = Arr::pluck($this->steps, 'name');

        if (is_string($step) && in_array($step, $stepNames)) {
            $this->active = array_search($step, $stepNames);

            return;
        }

        if (is_int($step) && 0 <= $step && $step <= count($this->steps) - 1) {
            $this->active = $step;
        }
    }

    public function run(): void
    {
        $completed = $this->{"run{$this->currentStep()['name']}"}();

        $this->currentStep()['completed'] = $completed;

        if ($completed) {
            $this->next();
        }
    }

    public function render()
    {
        $this->shareVars();

        return view('livewire.register');
    }

    /**
     * @return array|mixed
     */
    protected function currentStep()
    {
        return $this->steps[$this->active];
    }

    protected function shareVars()
    {
        $methodName = "shareVars{$this->currentStep()['name']}";

        if (method_exists($this, $methodName)) {
            $this->$methodName();
        }
    }
}
