<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Livewire\Component;

abstract class MultiStepForm extends Component
{
    /**
     * Index of the currently active step.
     */
    public int $active = 0;

    /**
     * List of the steps.
     */
    public array $steps = [];

    public function next(): void
    {
        $this->goToStep($this->active + 1);
    }

    public function goToStep(int | string $step): void
    {
        if (is_int($step) && 0 <= $step && $step <= count($this->steps) - 1) {
            $this->active = $step;

            return;
        }

        if (in_array($step, $this->steps)) {
            $this->active = array_search($step, $this->steps);

            return;
        }

        throw new InvalidArgumentException('The requested step does not exist.');
    }

    public function run(): void
    {
        $startingStep = $this->active;

        $methodName = 'run' . Str::title(Str::camel($this->currentStep()));

        $this->$methodName();

        if ($startingStep === $this->active) {
            $this->next();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->shareVars();

        return $this->renderView();
    }

    abstract public function renderView(): \Illuminate\Contracts\View\View;

    protected function currentStep(): string
    {
        return $this->steps[$this->active];
    }

    protected function shareVars()
    {
        $methodName = "shareVars{$this->currentStep()}";

        if (method_exists($this, $methodName)) {
            $this->$methodName();
        }
    }
}
