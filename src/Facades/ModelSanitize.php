<?php

namespace Touhidurabir\ModelSanitize\Facades;

use Illuminate\Support\Facades\Facade;

class ModelSanitize extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'model-sanitize';
    }
}