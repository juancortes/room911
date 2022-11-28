<?php

namespace App\Repositories;

use App\Models\DepratamentoProduccion;
use App\Repositories\BaseRepository;

/**
 * Class DepratamentoProduccionRepository
 * @package App\Repositories
 * @version November 26, 2022, 8:08 pm UTC
*/

class DepratamentoProduccionRepository extends BaseRepository
{

    /**
     * @param array $attribute
     * @param $value
     * @return mixed
     */
    public function where($attribute) {
        return $this->model->where($attribute);
    }
    
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepratamentoProduccion::class;
    }
}
