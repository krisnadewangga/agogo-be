<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\KompresFoto;
use App\User;
use App\Level;
use App\Role;
use Auth;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function($request,$next){
            if(Gate::allows('add_users')) return $next($request);
            abort(404, 'Halaman Tidak Ditemukan');
        });

    }
    
    public function index()
    {
       $administrator = User::whereNotIn('level_id',['6'])->get();
       // $find = User::where('id',20)->first();
       // $role = $find->Roles->pluck('level_id')->toArray();

       // return $role;

       $administrator->map(function($administrator){
         $sel_role = Role::where(['user_id' => $administrator->id])->pluck('level_id');
         $tampil_rol = Level::selectRaw("GROUP_CONCAT(level) as tampil_rol")->whereIn('id', $sel_role)->get();
         
         $administrator['roles'] = $sel_role;
         $administrator['tampil_rol'] =   $tampil_rol[0]['tampil_rol'];

         return $administrator;
       });  

       $levels = Level::whereNotIn('id',['6'])->get();
  

       $menu_active = "user|admin|0";
       return view('user.administrator', compact('administrator','menu_active','levels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = $request->all();
        $rules = ['name' => 'required',
                  'email' => 'required|unique:users', 
                  'password' => 'required|min:6',
                  'roles' => 'required|array|min:1'
                 ];

        if(isset($req['foto'])){
            $rules['foto' ] = 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG';
        }

        $vaildator = \Validator::make($req,$rules);

        if($vaildator->fails()){
            return redirect()->back()->withErrors($vaildator)->withInput()->with('gagal','simpan');
        }

        if(in_array('7', $req['roles'])){
            $req['level_id'] = '7';
        }else{
            $req['level_id'] = '2';
        }
        
        $req['password'] = bcrypt($request->password);
        
        if(isset($req['foto'])){
            $uploadFoto = KompresFoto::Upload($req['foto'],'user');
            $req['foto'] = $uploadFoto;
        }

        $insert = User::create($req);

        $level_id = [];
        foreach ($req['roles'] as $key) {
            $level_id[] = ['level_id' => $key];
        }

        $find = User::findOrFail($insert->id);
        $find->Roles()->createMany($level_id);

        return redirect()->back()->with("success","Berhasil Buat User");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $req = $request->all();
       

        $req_roles = $request->only(['roles']);

        $rules = ['name' => 'required', 'email' => 'required', 'status_aktif' => 'required', 
                  'roles' => 'required|array|min:1'];

        if(isset($req['password'])){
            $rules['password'] = 'required|min:6';
        }

        if(isset($req['foto'])){
            $rules['foto' ] = 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG';
        }

        $validator = \Validator::make($req,$rules);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }
        
       
        if(!isset($req['password'])){
            $req = $request->except(['password']);
        }else{
            $req['password'] = bcrypt($request->password);
        }

       

        $find = User::findOrFail($req['id']);

        $roles_new = array_map('intval',$req_roles['roles']);
        $role_saat_ini = Role::where(['user_id' => $find->id])->pluck('level_id')->toArray();

        if(count($roles_new) > count($role_saat_ini) ){
            $a = array_diff($roles_new,$role_saat_ini);
        }else if(count($roles_new) < count($role_saat_ini)){
            $a = array_diff($role_saat_ini,$roles_new);
        }else{
            $a = array_diff($roles_new,$role_saat_ini);
        }

        
        if(isset($req['foto'])){
            if(!empty($find->foto)){
                $hapusFoto = KompresFoto::HapusFoto($find->foto); 
            }
            
            $uploadFoto = KompresFoto::Upload($req['foto'],'user');
            $req['foto'] = $uploadFoto;
        }

        unset($req['roles']);
        $find->update($req);
        
        if(count($a) > 0){
            $find->Roles()->delete();
            $level_id = [];
            foreach ($req_roles['roles'] as $key) {
                $level_id[] = ['level_id' => $key];
            }
            $find->Roles()->createMany($level_id);
        }

        return redirect()->back()->with('success','Berhasil Update User');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $find = User::findOrFail($id);
        $find->delete();
        return redirect()->back()->with('success','Berhasil Hapus User');
    }
}
