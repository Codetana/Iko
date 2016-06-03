<?php

namespace App\Tango\Database;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    //
    protected $table = 'settings';

    protected $fillable = [
    	'forum_name',
    	'forum_theme'
    ];
}
