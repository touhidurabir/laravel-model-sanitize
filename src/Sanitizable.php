<?php

namespace Touhidurabir\ModelSanitize;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Touhidurabir\ModelSanitize\Builder\SanitizableQueryBuilder;

trait Sanitizable {

    /**
     * Sanitize data list to model fillables
     *
     * @param  array   $data
     * @return array
     */
    public function sanitizeToModelFillable(array $data) {

        $fillable     = $this->getFillable();

        $fillables = ! empty($fillable) 
                        ? $fillable 
                        : array_diff(
                            array_diff(
                                Schema::getColumnListing($this->getTable()), 
                                $this->getGuarded()
                            ), 
                            $this->getHidden()
                        );

        return array_intersect_key($data, array_flip($fillables));
    }


    /**
     * Get the extra data that passed to model to create/update
     *
     * @param  array   $data
     * @return array
     */
    public function extraData(array $data) {
        
        $modelFillables = $this->sanitizeToModelFillable($data);
        
        return array_diff_key($data, $modelFillables);
    }


    /**
     * Get the sanitized data/attributes for this model 
     *
     * @param  array   $data
     * @return array
     */
    public static function sanitize(array $attributes = []) {

        return (new static)->sanitizeToModelFillable($attributes);
    }


    /**
     * Get the gibberish data/attributes for this model 
     *
     * @param  array   $data
     * @return array
     */
    public static function gibberish(array $attributes = []) {

        return (new static)->extraData($attributes);
    }


    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Touhidurabir\ModelSanitize\Builder\SanitizableQueryBuilder|static
     */
    public function newEloquentBuilder($query) {
        
        return new SanitizableQueryBuilder($query);
    }


}