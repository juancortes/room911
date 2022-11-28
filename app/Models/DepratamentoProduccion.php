<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DepratamentoProduccion
 * @package App\Models
 * @version November 26, 2022, 8:08 pm UTC
 *
 */
class DepratamentoProduccion extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'depratamento_produccions';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
