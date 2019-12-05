<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
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
    }
    
    public function index()
    {
       $administrator = User::where('level_id','2')->get();
       $menu_active = "user|admin|0";
       return view('user.administrator', compact('administrator','menu_active'));
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

        $vaildator = \Validator::make($req,['name' => 'required', 'email' => 'required|unique:users', 'password' => 'required|min:6']);

        if($vaildator->fails()){
            return redirect()->back()->withErrors($vaildator)->withInput()->with('gagal','simpan');
        }

        $req['level_id'] = '2';
        $req['password'] = bcrypt($req['pasword']);
        $insert = User::create($req);

        return redirect()->back()->with("success","Berhasil Buat Administrator");
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
        if(isset($req['password'])){
            $validator = \Validator::make($req,['name' => 'required', 'email' => 'required', 'status_aktif' => 'required', 'password' => 'required|min:6' ]);
        }else{
            $validator = \Validator::make($req,['name' => 'required', 'email' => 'required', 'status_aktif' => 'required']);
        }

        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }
        
        if(!isset($req['password'])){
            $req = $request->except(['password']);
        }else{
            $req['password'] = bcrypt($request->password);
        }

        $find = User::findOrFail($req['id']);
        $find->update($req);
        
        return redirect()->back()->with('success','Berhasil Update Administrator');
        
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
        return redirect()->back()->with('success','Berhasil Hapus Administrator');
    }
}
