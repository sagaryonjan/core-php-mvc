<?php

namespace System;


class Loader
{
    /**
     * Application Object
     *
     * @var \System\Application
     */
    private $app;

    /**
     * Controllers Container
     *
     * @var array
     */
    private $controllers = [];

    /**
     * Models Container
     *
     * @var array
     */
    private $models = [];

    /**
     * Constructor
     *
     * @param \System\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Call the given controller with the given method
     * and pass the given arguments to the controller method
     *
     * @param string $controller
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function action($controller, $method, array $arguments)
    {
        $object = $this->controller($controller);

        return call_user_func([$object, $method], $arguments);

    }

    /**
     * Call the given controller
     *
     * @param string $controller
     * @return object
     */
    public function controller($controller)
    {
        $controller = $this->getControllerName($controller);

        if(!$this->hasController($controller))
            $this->addController($controller);

        return $this->getController($controller);
    }

    /**
     * Determine if the given class/controller exists
     * in the controllers container
     *
     * @param string $controller
     * @return bool
     */
    private function hasController($controller)
    {
        return array_key_exists($controller, $this->controllers);
    }

    /**
     * Create new object for the given controller and store it
     * in controllers container
     *
     * @param string $controller
     * @return void
     */
    private function addController($controller)
    {
        $object = new $controller($this->app);

        $this->controllers[$controller] = $object;
    }

    /**
     * Get the controller object
     *
     * @param string $controller
     * @return object
     */
    private function getController($controller)
    {
        return $this->controllers[$controller];
    }

    /**
     * Get the full class name for the given controller
     *
     * @param string $controller
     * @return string
     */
    private function getControllerName($controller)
    {

        $controller = 'App\\Controllers\\'.$controller;

        return $controller;
    }

    /**
     * Call the given model
     *
     * @param string $model
     * @return object
     */
    public function model($model)
    {

    }

    /**
     * Determine if the given class exists in the models container
     *
     * @param string $model
     * @return bool
     */
    private function hasModel($model)
    {

    }

    /**
     * Create new Object for the given model and store it
     * in models container
     * @param string $model
     * @return void
     */
    private function addModel($model)
    {

    }

    /**
     * Get the model object
     *
     * @param string $model
     * @return void
     */
    private function getModel($model)
    {

    }

}