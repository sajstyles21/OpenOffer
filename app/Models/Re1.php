<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Re1 extends Model
{
    use HasFactory;

    protected $table = "re1";

    protected $connection = 'mysql2';

    protected $appends = ['days'];

    public function getDaysAttribute()
    {
        return "30";
    }

}
