<?php

namespace Touhidurabir\ModelSanitize\Builder;

use Closure;
use Illuminate\Database\Eloquent\Builder as BaseBuilder;

class SanitizableQueryBuilder extends BaseBuilder
{
    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  \Closure|array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function updateOrCreate(array $attributes, $values = [])
    {
        return parent::updateOrCreate($attributes, $this->sanitizeValues($values));
    }


    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array  $attributes
     * @param  \Closure|array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function firstOrCreate(array $attributes = [], $values = [])
    {
        return parent::firstOrCreate($attributes, $this->sanitizeValues($values));
    }


    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array  $attributes
     * @param  \Closure|array  $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function firstOrNew(array $attributes = [], $values = [])
    {
        return parent::firstOrNew($attributes, $this->sanitizeValues($values));
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


    /**
     * Sanitize the given values, deferring the sanitization if values are given as a Closure.
     *
     * @param  \Closure|array  $values
     * @return \Closure|array
     */
    protected function sanitizeValues($values)
    {
        if ($values instanceof Closure) {

            $model = $this->model;

            return function () use ($values, $model) {
                return $model->sanitizeToModelFillable($values());
            };
        }

        return $this->model->sanitizeToModelFillable($values);
    }
}
