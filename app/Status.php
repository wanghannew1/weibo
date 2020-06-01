<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	/*
    与用户的一对多关系
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
