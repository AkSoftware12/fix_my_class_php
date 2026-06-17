<?php

namespace App\Events;

use App\Models\Homework;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HomeworkAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(public Homework $homework)
    {
    }
}
