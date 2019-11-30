<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Stocker;
use Auth;

class StockerController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request, $id)
    {
        $req = $request->all();
        $validator = \Validator::make($req,['jumlah' => 'required|numeric' ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','stock');
        }

        $req['input_by'] = Auth::User()->name;
        $req['item_id'] = $id;

        $findItem = Item::findOrFail($id);
        $newStock = $findItem->stock + $req['jumlah'];

        $insertStocker = Stocker::create($req);
        $updateStock = $findItem->update(['stock' => $newStock]);

        return redirect()->back()->with('success_up_stock','Berhasil Menambahkan Stock')->with('success_tab','stock');
    }

   
    public function destroy($id)
    {
        $find = Stocker::findOrFail($id);
        $findItem = Item::findOrFail($find->item_id);
        $newStock = $findItem->stock - $find->jumlah;

        $findItem->update(['stock' => $newStock]);
        $find->delete();

        return redirect()->back()->with('success_del_stock','Berhasil Hapus Stock');

    }
}
