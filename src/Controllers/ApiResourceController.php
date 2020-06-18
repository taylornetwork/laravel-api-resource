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
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function index(Request $request): Collection
    {
        if($request->has('with')) {
            return $this->staticModel()::with($request->with)->get();
        }

        return $this->staticModel()::all();
    }

    /**
     * Get model instance from id
     *
     * @param $id
     * @return ApiResourceModel
     * @throws ModelNotFoundException
     */
    public function show($id): ApiResourceModel
    {
        return $this->staticModel()::find($id);
    }

    /**
     * Store the newly created data
     *
     * @param Request $request
     * @return ApiResourceModel
     * @throws ModelNotFoundException
     */
    public function store(Request $request): ApiResourceModel
    {
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
        $this->staticModel()::find($id)->delete();
        return $this->ok();
    }
}