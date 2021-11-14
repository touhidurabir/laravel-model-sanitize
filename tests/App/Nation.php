<?php

namespace Touhidurabir\ModelSanitize\Tests\App;

use Touhidurabir\ModelSanitize\Sanitizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nation extends Model {

    use SoftDeletes;

    use Sanitizable;

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'nations';


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