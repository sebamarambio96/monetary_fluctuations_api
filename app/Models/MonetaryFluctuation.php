<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MonetaryFluctuation
 * 
 * @property int|null $id
 * @property float|null $date
 * @property int|null $value_clp
 * @property int $id_currencies
 * 
 * @property Currency $currency
 *
 * @package App\Models
 */
class MonetaryFluctuation extends Model
{
	protected $table = 'monetary_fluctuations';
	public $timestamps = false;

	protected $casts = [
		'date' => 'float',
		'value_clp' => 'int',
		'id_currencies' => 'int'
	];

	protected $fillable = [
		'date',
		'value_clp',
		'id_currencies'
	];

	public function currency()
	{
		return $this->belongsTo(Currency::class, 'id_currencies');
	}
}
