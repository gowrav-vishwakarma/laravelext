<?php

namespace LE;

use Illuminate\Database\Eloquent\Model as EModel;
use Carbon\Carbon;

class Model extends EModel {


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
		
		$query->where($created_field,'>',$on_date->toDateString());
		$query->where($created_field,'<',$to_date->toDateString());
	}
}