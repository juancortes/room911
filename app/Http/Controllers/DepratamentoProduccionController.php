<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepratamentoProduccionRequest;
use App\Http\Requests\UpdateDepratamentoProduccionRequest;
use App\Repositories\DepratamentoProduccionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class DepratamentoProduccionController extends AppBaseController
{
    /** @var DepratamentoProduccionRepository $depratamentoProduccionRepository*/
    private $depratamentoProduccionRepository;

    public function __construct(DepratamentoProduccionRepository $depratamentoProduccionRepo)
    {
        $this->depratamentoProduccionRepository = $depratamentoProduccionRepo;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the DepratamentoProduccion.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
      $depratamentoProduccions = $this->depratamentoProduccionRepository->all();
      $order     = "name DESC";
      $attribute = [];

      if(count($request->get('f',[])) == 0)
      {
        $depratamentoProduccion = $this->depratamentoProduccionRepository
                                ->where($attribute)
                                ->orderByRaw($order)
                                ->paginate(10);
      }
      else
      {
        foreach ($request->get('f',[]) as $key => $value) {
          if($key == 'order_by' && isset($value))
          {
            $order = $value;
          }
          if($key == 'order_dir' && isset($value))
          {
            $order = str_replace('ASC','',$order);
            $order = str_replace('DESC','',$order);
            $order .= " ".$value;
          }
          if($key == 'id' && isset($value))
          {
            $value = explode('O', strtoupper($value));
            if(count($value) > 1)
              $value = $value[1];
            else 
              $value = $value[0];
            
            if(is_numeric($value))
              $attribute[] = ["id",'=',$value];
          }
          if($key == 'name' && isset($value))
          {
            $attribute[] = ["name",'ILIKE','%'.$value.'%'];
          }          
        }

        $depratamentoProduccion = $this->depratamentoProduccionRepository
                                ->where($attribute)
                                ->orderByRaw($order)
                                ->paginate(10);
      }

      $grid = new \Aginev\Datagrid\Datagrid($depratamentoProduccion,$request->get('f',[]));

      // Then we are starting to define columns
      $grid  
           ->setColumn('id', 'Código', [
              'sortable'    => true,
              'has_filters' => true,
              // Wrapper closure will accept two params
              // $value is the actual cell value
              // $row are the all values for this row
              'wrapper'     => function ($value, $row) {
                  return 'O'.$value;
              }
          ])
           ->setColumn('name', 'Nombre', [
              // If you want to have role_id in the URL query string but you need to show role.name as value (dot notation for the user/role relation)
              'sortable'    => true,
              'has_filters' => true,
              // Pass array of data to the filter. It will generate select field.
              //'filters'     => TiposSolicitud::all()->lists('tipo', 'id_tipo_solicitud'),
              // Define HTML attributes for this column
              // 'attributes'  => [
              //     'class'         => 'custom-class-here',
              //     'data-custom'   => 'custom-data-attribute-value',
              // ],
          ])          
         
          // Setup action column
          ->setActionColumn([
              // Define HTML attributes for this column
              'attributes'  => [
                  'style'   => 'width:197px;',
              ],
              'wrapper' => function ($value, $row) {
                  return '
                      <a href="'.route("depratamentoProduccions.show", [$row->id]).'"  title="Ver" class="btn btn-xs"><i class="far fa-eye"></i></a>
                      <a href="'.route("depratamentoProduccions.edit", [$row->id]).'"  title="Editar" class="btn btn-xs"><i class="fas fa-edit"></i></a>
                      <a href="#" onclick="eliminar(\'depratamentoProduccions\','. $row->id .')" title="Eliminar" class="btn btn-xs"><i class="fas fa-trash-alt"></i></a>
                         ';
              }
          ]);

      return view('depratamento_produccions.index')
            ->with('depratamentoProduccions', $depratamentoProduccions)
            ->with('grid',$grid);
    }

    /**
     * Show the form for creating a new DepratamentoProduccion.
     *
     * @return Response
     */
    public function create()
    {
        return view('depratamento_produccions.create');
    }

    /**
     * Store a newly created DepratamentoProduccion in storage.
     *
     * @param CreateDepratamentoProduccionRequest $request
     *
     * @return Response
     */
    public function store(CreateDepratamentoProduccionRequest $request)
    {
        $input = $request->all();

        $depratamentoProduccion = $this->depratamentoProduccionRepository->create($input);

        Flash::success('Depratamento Produccion saved successfully.');

        return redirect(route('depratamentoProduccions.index'));
    }

    /**
     * Display the specified DepratamentoProduccion.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $depratamentoProduccion = $this->depratamentoProduccionRepository->find($id);

        if (empty($depratamentoProduccion)) {
            Flash::error('Depratamento Produccion not found');

            return redirect(route('depratamentoProduccions.index'));
        }

        return view('depratamento_produccions.show')->with('depratamentoProduccion', $depratamentoProduccion);
    }

    /**
     * Show the form for editing the specified DepratamentoProduccion.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $depratamentoProduccion = $this->depratamentoProduccionRepository->find($id);

        if (empty($depratamentoProduccion)) {
            Flash::error('Depratamento Produccion not found');

            return redirect(route('depratamentoProduccions.index'));
        }

        return view('depratamento_produccions.edit')->with('depratamentoProduccion', $depratamentoProduccion);
    }

    /**
     * Update the specified DepratamentoProduccion in storage.
     *
     * @param int $id
     * @param UpdateDepratamentoProduccionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepratamentoProduccionRequest $request)
    {
        $depratamentoProduccion = $this->depratamentoProduccionRepository->find($id);

        if (empty($depratamentoProduccion)) {
            Flash::error('Depratamento Produccion not found');

            return redirect(route('depratamentoProduccions.index'));
        }

        $depratamentoProduccion = $this->depratamentoProduccionRepository->update($request->all(), $id);

        Flash::success('Depratamento Produccion updated successfully.');

        return redirect(route('depratamentoProduccions.index'));
    }

    /**
     * Remove the specified DepratamentoProduccion from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $depratamentoProduccion = $this->depratamentoProduccionRepository->find($id);

        if (empty($depratamentoProduccion)) {
            Flash::error('Depratamento Produccion not found');

            return redirect(route('depratamentoProduccions.index'));
        }

        $this->depratamentoProduccionRepository->delete($id);

        Flash::success('Depratamento Produccion deleted successfully.');

        return redirect(route('depratamentoProduccions.index'));
    }
}
