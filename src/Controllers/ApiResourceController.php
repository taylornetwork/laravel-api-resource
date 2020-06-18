<?php


namespace TaylorNetwork\LaravelApiResource\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use TaylorNetwork\LaravelApiResource\Exceptions\ModelNotFoundException;
use TaylorNetwork\LaravelApiResource\Models\ApiResourceModel;

abstract class ApiResourceController extends ApiController
{
    /**
     * Model class
     *
     * @var string
     */
    protected $model;

    /**
     * Disabled methods
     *
     * @var array
     */
    protected $disabled = [];


    /**
     * Enabled methods
     *
     * @var array
     */
    protected $enabled = ['*'];

    /**
     * Get or guess the model class name.
     *
     * @return string
     * @throws ModelNotFoundException
     */
    public function getModelClass(): string
    {
        if(!isset($this->model)) {
            $controller = class_basename($this);
            $guessModel = str_replace('Controller', '', $controller);
            $namespace = Config::get('api_resource.model_namespace', 'App\\');
            if(class_exists($namespace.$guessModel)) {
                return $namespace.$guessModel;
            }
            throw new ModelNotFoundException('Model for controller '.get_class($this).' could not be found.');
        }

        return $this->model;
    }

    /**
     * Get a new instance of the model
     *
     * @return ApiResourceModel
     * @throws ModelNotFoundException
     */
    public function model(): ApiResourceModel
    {
        $class = $this->getModelClass();
        return new $class();
    }

    /**
     * Get model name
     *
     * @return string
     * @throws ModelNotFoundException
     */
    public function staticModel(): string
    {
        return $this->getModelClass();
    }

    /**
     * Get all model instances
     *
     * @param Request $request
     * @return Collection|Response
     * @throws ModelNotFoundException
     */
    public function index(Request $request)
    {
        if(!$this->isEnabled('index')) {
            return $this->notImplemented();
        }

        if($request->has('with')) {
            return $this->staticModel()::with($request->with)->get();
        }

        return $this->staticModel()::all();
    }

    /**
     * Get model instance from id
     *
     * @param $id
     * @return ApiResourceModel|Response
     * @throws ModelNotFoundException
     */
    public function show($id)
    {
        if(!$this->isEnabled('show')) {
            return $this->notImplemented();
        }

        return $this->staticModel()::find($id);
    }

    /**
     * Store the newly created data
     *
     * @param Request $request
     * @return ApiResourceModel|Response
     * @throws ModelNotFoundException
     */
    public function store(Request $request)
    {
        if(!$this->isEnabled('store')) {
            return $this->notImplemented();
        }

        $request->validate($this->model()->getValidationRules());
        return $this->staticModel()::create($request->only($this->model()->getFillable()));
    }

    /**
     * Update the model instance
     *
     * @param $id
     * @param Request $request
     * @return Response
     * @throws ModelNotFoundException
     */
    public function update($id, Request $request): Response
    {
        if(!$this->isEnabled('update')) {
            return $this->notImplemented();
        }

        $request->validate($this->model()->getValidationRules());
        $this->staticModel()::find($id)->update($request->only($this->model()->getFillable()));
        return $this->ok();
    }

    /**
     * Delete an instance of the model
     *
     * @param $id
     * @return Response
     * @throws ModelNotFoundException
     */
    public function destroy($id): Response
    {
        if(!$this->isEnabled('destroy')) {
            return $this->notImplemented();
        }

        $this->staticModel()::find($id)->delete();
        return $this->ok();
    }

    /**
     * Determine if the given method is enabled.
     *
     * @param string $method
     * @return bool
     */
    protected function isEnabled(string $method): bool
    {
        if(in_array('*', $this->enabled)) {
            return !in_array($method, $this->disabled);
        }
        return in_array($method, $this->enabled);
    }
}