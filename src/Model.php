<?php

namespace LE;

use Illuminate\Database\Eloquent\Model as EModel;
use Carbon\Carbon;

class Model extends EModel {

	/**
	 * To check if any model is in active_status is a very frequent requirement,
	 * Values of each field can be scalar or array to check, all fields with ANDed operator and all values for same field with ORed
	 * you can even set it to false or null to pass activity checks for models
	 * 
	 * Ex: $active_check = ['is_active'=>[1,'Yes'],'status'=>'Delivered']
	 * 
	 * @var array
	 */
	
	public $active_check = ['is_active'=>1];

	/**
	 * Same as active_check but checks for Inactive in scope
	 * @var array
	 */
	public $inactive_check = ['is_active'=>0];


	public $validations	= null;
	public $validator = null;

	public function __construct(array $attributes = [])
		if($this->validations && is_array($this->validations)) {
			$this->validator = \Validator::make($this->toArray(), $this->validations);
		}
		parent::__construct($attributes);
	}

	public function save(array $options = []){
		if(!$this->isValidated()) dd($this->validator->errors());
		parent::save($options);
	}

	function isValidated(){
		if($this->validator) {
	        return !$this->validator->fails();
		}
		return true;
	}

	// ======= Active InActive based scopes ===========//
	function scopeActive($query){
		if($this->active_check !== null || $this->active_check !== false){
			foreach ($this->active_check as $field => $value) {
				if(is_array($value)){
					$query->whereIn($field,$value);
				}else{
					$query->where($field,$value);
				}
			}
		}
	}

	function scopeInActive($query){
		if($this->inactive_check !== null || $this->inactive_check !== false){
			foreach ($this->inactive_check as $field => $value) {
				if(is_array($value)){
					$query->whereIn($field,$value);
				}else{
					$query->where($field,$value);
				}
			}
		}
	}

	// ======= Scope Methods based on CREATED_AT values =========== //
	/**
	 * Use to apply scope based on date range INCLUDING TIME ALSO, like
	 * $model->createdBetween('now','yesterday') or
	 * $model->createdBetween('2019-01-01',$another_date_variable)
	 * 
	 * @param  [Builder] 						$query         		Default passed to scope function by laravel
	 * @param  [string or DateTime or Carbon] 	$from_date     		first date to pass, will be used as '>='
	 * @param  [string or DateTime or Carbon] 	$to_date       		last range of date, will be used as '<'
	 * @param  [string] 						$created_field 		[Optional] Will pick default CREATED_AT field from timestemps laravel settings but you can override
	 * @return [Builder]                							Returns Builder to chain further
	 */
	function scopeCreatedBetween($query,$from_date,$to_date,$created_field=null){
		if(!$created_field) $created_field=Static::CREATED_AT;
		
		$from_date = Carbon::parse($from_date);
		$to_date = Carbon::parse($to_date);

		$query->where($created_field,'>=',$from_date->toDateTimeString());
		$query->where($created_field,'<',$to_date->toDateTimeString());
	}

	/**
	 * Use to apply scope based on date range WITHOUT TIME, JUST DATE, like
	 * $model->createdBetween('now','yesterday') // Here Now will be converted to today's date only
	 * $model->createdBetween('2019-01-01',$another_date_variable)
	 * 
	 * @param  [Builder] 						$query         		Default passed to scope function by laravel
	 * @param  [string or DateTime or Carbon] 	$from_date     		first date to pass, will be used as '>='
	 * @param  [string or DateTime or Carbon] 	$to_date       		last range of date, will be used as '<'
	 * @param  [string] 						$created_field 		[Optional] Will pick default CREATED_AT field from timestemps laravel settings but you can override
	 * @return [Builder]                							Returns Builder to chain further
	 */
	function scopeCreatedBetweenDates($query, $from_date, $to_date, $created_field=null){
		if(!$created_field) $created_field=Static::CREATED_AT;

		$from_date = Carbon::parse($from_date);
		$to_date = Carbon::parse($to_date);
		
		$query->where($created_field,'>',$from_date->toDateString());
		$query->where($created_field,'<=',$to_date->toDateString());
	}

	/**
	 * A shortcut for CreatedBetweenDates(date,nextDate) // Works on date only, not datetime
	 * $model->createdOn('2019-01-01')
	 * 
	 * @param  [Builder] 						$query         		Default passed to scope function by laravel
	 * @param  [string or DateTime or Carbon] 	$on_date     		first date to pass, will be used as '>='
	 * @param  [string] 						$created_field 		[Optional] Will pick default CREATED_AT field from timestemps laravel settings but you can override
	 * @return [Builder]                							Returns Builder to chain further
	 */
	function scopeCreatedOn($query, $on_date, $created_field=null){
		if(!$created_field) $created_field=Static::CREATED_AT;

		$on_date = Carbon::parse($on_date);
		$to_date = Carbon::parse($on_date)->addDay(1);
		
		$query->where($created_field,'>=',$on_date->toDateString());
		$query->where($created_field,'<',$to_date->toDateString());
	}
}