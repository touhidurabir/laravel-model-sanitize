<?php

namespace Touhidurabir\ModelSanitize\Tests\App;

use Touhidurabir\ModelSanitize\Sanitizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model {

    use SoftDeletes;

    use Sanitizable;

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'states';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'city_counts',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
    ];

}