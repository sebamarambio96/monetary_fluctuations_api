<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InterestYear
 * 
 * @property int|null $year
 * @property int|null $status
 *
 * @package App\Models
 */
class InterestYear extends Model
{
	protected $table = 'interest_years';
	protected $primaryKey = 'year';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'status'
	];
}
