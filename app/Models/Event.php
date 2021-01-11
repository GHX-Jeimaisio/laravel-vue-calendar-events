<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function hasManyEventSchedule(){
        return $this->hasMany('App\Models\EventSchedule');
    }
}
