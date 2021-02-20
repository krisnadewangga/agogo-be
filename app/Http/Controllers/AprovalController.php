<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Aproval;
use Auth;

class AprovalController extends Controller
{

    public function __construct()
    {
        return $this->middleware('auth');
        $this->middleware(function($request,$next){
            if(Gate::allows('add_users')) return $next($request);
            abort(404, 'Halaman Tidak Ditemukan');
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu_active = "user|aproval|0";
        $users = User::whereNotIn('level_id',['6','8'])->where('status_aktif','1')->get();
        $list_aproval = $this->listAproval();
        // return $list_aproval;

        $aproval = Aproval::orderBy('rule','ASC')->get();
        $aproval->map(function($aproval) use ($list_aproval) {
            $aproval['rule_name'] = $list_aproval[$aproval->rule]['text'];
            return $aproval;
        });


        return view('user.aproval',compact('menu_active','users','list_aproval','aproval'));
    }

    public function listAproval()
    {
        $list = [ '1' => ['id' => '1', 'text' => 'Kasir'],
                  '2' => ['id' => '2', 'text' => 'Pemesanan'],
                  '3' => ['id' => '3', 'text' => 'Produksi'],
                  '4' => ['id' => '4', 'text' => 'Opname']
                ];

        return $list;
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
        $rules = ['rule' => 'required',
                  'user' => 'required'
                 ];

        $vaildator = \Validator::make($req,$rules);

        if($vaildator->fails()){
            return redirect()->back()->withErrors($vaildator)->withInput()->with('gagal','simpan');
        }

        foreach ($req['user'] as $key) {
            $insert = Aproval::insert(['user_id' => $key, 
                                       'rule' => $req['rule']
                                      ]);
        }

        return redirect()->back()->with('success','Berhasil Buat Aproval');
    }

   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $find = Aproval::findOrfail($id);
        $find->delete();
        return redirect()->back()->with('success','Berhasil Hapus Aproval');
    }
}
