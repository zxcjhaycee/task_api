<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    //
    protected $table = 'tasks';

    protected $guarded = [];

    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    public function tasks(){
        return $this->belongsTo('App\User');
    }
}
