<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingAttribute extends Model
{
    use HasFactory;

    protected $table = "mapping_attribute";

    public function Mapping()
    {
        return $this->belongsTo('App\Models\Mapping', 'id');
    }
}
