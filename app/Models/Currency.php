<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Currency
 * 
 * @property int|null $id
 * @property string|null $name
 * @property int|null $status
 *
 * @package App\Models
 */
class Currency extends Model
{
	protected $table = 'currencies';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'name',
		'status'
	];
}
