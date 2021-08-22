<?php

namespace App\Models;

use App\Models\MappingAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    use HasFactory;

    protected $table = "mapping";

    public function MappingAttribute()
    {
        return $this->hasOne(MappingAttribute::class, 'id', 'mapping_attribute_id');

    }

}
