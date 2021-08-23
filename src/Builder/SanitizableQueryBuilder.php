<?php

namespace Touhidurabir\ModelSanitize\Builder;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;

class SanitizableQueryBuilder extends BaseBuilder {

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function updateOrCreate(array $attributes, array $values = []) 
    {
        return parent::updateOrCreate($attributes, $this->model->sanitizeToModelFillable($values));
    }


    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function firstOrCreate(array $attributes = [], array $values = []) 
    {
        return parent::firstOrCreate($attributes, $this->model->sanitizeToModelFillable($values));
    }


    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function firstOrNew(array $attributes = [], array $values = []) 
    {
        return parent::firstOrNew($attributes, $this->model->sanitizeToModelFillable($values));
    }


    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model|$this
     */
    public function create(array $attributes = []) 
    {
        return parent::create($this->model->sanitizeToModelFillable($attributes));
    }


    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model|$this
     */
    public function forceCreate(array $attributes) 
    {
        return parent::forceCreate($this->model->sanitizeToModelFillable($attributes));
    }


    /**
     * Update records in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values) 
    {
        return parent::update($this->model->sanitizeToModelFillable($values));
    }
}