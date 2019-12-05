<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Level;
use Auth;

class LevelController extends Controller
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
        $level = Level::where('status_aktif','1')->get();
        $menu_active = "user|level|0";
        return view('user.level', compact('level','menu_active'));
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
        $validator = \Validator::make($req, ['level' => 'required|unique:level']);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->with('gagal','simpan');
        }

        $insert = Level::create($req);
        return redirect()->back()->with("success","Berhasil Buat Level");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return "ini show";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
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
        // return "level";
       $req = $request->all();

   
        $validator = \Validator::make($req,['level' => 'required', 'status_aktif' => 'required','id' => 'required']);   
      

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator,'edit')->with('gagal','update')->withInput();
        }

        $find = Level::findOrFail($req['id']);
        $update = $find->update(['level' => $req['level'], 'status_aktif' => $req['status_aktif']]);

        return  redirect()->back()->with("success","Berhasil Update Level");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $find = Level::findOrFail($id);
        $find->delete();
        return  redirect()->back()->with("success","Berhasil Hapus Level");
    }
}
