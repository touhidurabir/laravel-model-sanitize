<?php

namespace Touhidurabir\ModelSanitize\Tests\App;

use Touhidurabir\ModelSanitize\Sanitizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model {

    use SoftDeletes;

    use Sanitizable;

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'addresses';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'extras',
    ];

}