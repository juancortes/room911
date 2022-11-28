<?php

namespace App\Http\Controllers;    

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\DepratamentoProduccion;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;    

class UserController extends Controller
{

  private $userRepository;

  public function __construct(UserRepository $userRepo)
  {
      $this->userRepository = $userRepo;
      $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index(Request $request)
  {
    $order     = "name DESC";
    $attribute = [];

    if(count($request->get('f',[])) == 0)
    {
      $user = $this->userRepository
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
        if($key == 'email' && isset($value))
        {
          $attribute[] = ["email",'ILIKE','%'.$value.'%'];
        }
        if($key == 'departamento_produccion_id' && isset($value))
        {
            $attribute[] = ["departamento_produccion_id",'=',$value];
        }
      }

      $user = $this->userRepository
                              ->where($attribute)
                              ->orderByRaw($order)
                              ->paginate(10);
    }

    $grid = new \Aginev\Datagrid\Datagrid($user,$request->get('f',[]));

    // Then we are starting to define columns
    $grid  
         ->setColumn('id', 'Id', [
            'sortable'    => true,
            'has_filters' => true,
            // Wrapper closure will accept two params
            // $value is the actual cell value
            // $row are the all values for this row
            'wrapper'     => function ($value, $row) {
                return $value;
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
        ->setColumn('email', 'Correo', [
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
        ->setColumn('departamento_produccion_id', 'Departamento Productivo', [
            // If you want to have role_id in the URL query string but you need to show role.name as value (dot notation for the user/role relation)
            'sortable'    => true,
            'has_filters' => true,
            'refers_to'   => 'departamentProduccionId.name',
            'filters'     => DepratamentoProduccion::all()->pluck('name', 'id'),
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
                    <a href="'.route("users.show", [$row->id]).'"  title="Ver" class="btn btn-xs"><i class="far fa-eye"></i></a>
                    <a href="'.route("users.edit", [$row->id]).'"  title="Editar" class="btn btn-xs"><i class="fas fa-edit"></i></a>
                    <a href="#" onclick="eliminar(\'users\','. $row->id .')" title="Eliminar" class="btn btn-xs"><i class="fas fa-trash-alt"></i></a>
                       ';
            }
        ]);
    $data = User::orderBy('id','DESC')->paginate(5);

    return view('users.index',compact('data','grid'))

        ->with('i', ($request->input('page', 1) - 1) * 5);

  }

  

  /**

   * Show the form for creating a new resource.

   *

   * @return \Illuminate\Http\Response

   */

  public function create()

  {

      $depratamentProduccions = DepratamentoProduccion::pluck('name','id')->all();
      $roles = Role::pluck('name','name')->all();

      return view('users.create',compact('roles','depratamentProduccions'));

  }

  

  /**

   * Store a newly created resource in storage.

   *

   * @param  \Illuminate\Http\Request  $request

   * @return \Illuminate\Http\Response

   */

  public function store(Request $request)

  {

      $this->validate($request, [

          'name' => 'required',

          'email' => 'required|email|unique:users,email',

          'password' => 'required|same:confirm-password',

          'roles' => 'required'

      ]);

  

      $input = $request->all();

      $input['password'] = Hash::make($input['password']);

  

      $user = User::create($input);

      $user->assignRole($request->input('roles'));

  

      return redirect()->route('users.index')

                      ->with('success','User created successfully');

  }

  

  /**

   * Display the specified resource.

   *

   * @param  int  $id

   * @return \Illuminate\Http\Response

   */

  public function show($id)
  {

      $user = User::find($id);

      return view('users.show',compact('user'));

  }

  

  /**

   * Show the form for editing the specified resource.

   *

   * @param  int  $id

   * @return \Illuminate\Http\Response

   */

  public function edit($id)
  {

      $user = User::find($id);

      $roles = Role::pluck('name','name')->all();

      $userRole = $user->roles->pluck('name','name')->all();

      $depratamentProduccions = DepratamentoProduccion::pluck('name','id')->all();

      $userDepartament = $user->departamentProduccionId;

      return view('users.edit',compact('user','roles','userRole','depratamentProduccions','userDepartament'));

  }

  

  /**

   * Update the specified resource in storage.

   *

   * @param  \Illuminate\Http\Request  $request

   * @param  int  $id

   * @return \Illuminate\Http\Response

   */

  public function update(Request $request, $id)
  {

      $this->validate($request, [

          'name' => 'required',

          'email' => 'required|email|unique:users,email,'.$id,

          'password' => 'same:confirm-password',

          'roles' => 'required'

      ]);

  

      $input = $request->all();

      if(!empty($input['password'])){ 

          $input['password'] = Hash::make($input['password']);

      }else{

          $input = Arr::except($input,array('password'));    

      }

  

      $user = User::find($id);

      $user->update($input);

      DB::table('model_has_roles')->where('model_id',$id)->delete();

  

      $user->assignRole($request->input('roles'));

  

      return redirect()->route('users.index')

                      ->with('success','User updated successfully');

  }

  

  /**

   * Remove the specified resource from storage.

   *

   * @param  int  $id

   * @return \Illuminate\Http\Response

   */

  public function destroy($id)
  {

      User::find($id)->delete();

      return redirect()->route('users.index')

                      ->with('success','User deleted successfully');

  }
}