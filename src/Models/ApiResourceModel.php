<?php


namespace TaylorNetwork\LaravelApiResource\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ApiResourceModel extends Model
{
    /**
     * This model's validation rules.
     *
     * @var array
     */
    protected $validationRules = [];

    /**
     * Return all fillable attributes from table columns removing guarded if $fillable not set on model.
     *
     * @return array
     */
    public function getFillable()
    {
        $fillable = parent::getFillable();

        if(count($fillable)) {
            return $fillable;
        }

        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        return array_values(array_diff($columns, $this->getGuarded()));
    }

    /**
     * Get model's validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->validationRules;
    }
}