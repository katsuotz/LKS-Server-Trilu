<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $guarded = [];

    public function lists()
    {
        return $this->hasMany(BoardList::class, 'board_id');
    }

    public function members()
    {
        return $this->hasMany(BoardMember::class, 'board_id');
    }
}
