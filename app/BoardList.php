<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoardList extends Model
{
    protected $guarded = [];

    protected $with = ['cards'];

    public function cards()
    {
        return $this->hasMany(BoardCard::class, 'list_id');
    }
}
