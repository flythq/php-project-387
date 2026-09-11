<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class button extends Component
{
    public string $variant;

    public function __construct(string $variant = 'primary')
    {
        $this->variant = $variant;
    }

    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
