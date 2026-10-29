<?php

namespace Touhidurabir\ModelSanitize;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class ModelSanitizer
{
    /**
     * Get the sanitized data/attributes for the given model
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $model    model class name or instance
     * @param  array                                       $attributes
     *
     * @return array
     */
    public function sanitize($model, array $attributes = [])
    {

        return $this->resolveModel($model)->sanitizeToModelFillable($attributes);
    }


    /**
     * Get the gibberish data/attributes for the given model
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $model    model class name or instance
     * @param  array                                       $attributes
     *
     * @return array
     */
    public function gibberish($model, array $attributes = [])
    {

        return $this->resolveModel($model)->extraData($attributes);
    }


    /**
     * Resolve the model instance and make sure it uses the Sanitizable trait
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveModel($model)
    {

        if (is_string($model) && is_subclass_of($model, Model::class)) {

            $model = new $model();
        }

        if (! $model instanceof Model || ! in_array(Sanitizable::class, class_uses_recursive($model))) {

            throw new InvalidArgumentException(
                'The given model must be an Eloquent model class or instance using the ' . Sanitizable::class . ' trait.'
            );
        }

        return $model;
    }
}
