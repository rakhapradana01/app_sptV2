<?php

namespace App\View\Components\ui;

use Illuminate\View\Component;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class Pagination extends Component
{
    public LengthAwarePaginator $paginator;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function render()
    {
        return view('components.ui.pagination');
    }
}
