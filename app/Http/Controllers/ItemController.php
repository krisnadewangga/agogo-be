<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\KompresFoto;
use App\Item;
use App\GambarItem;
use App\Kategori;
use App\Stocker;
use Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $item = Item::where('status_aktif','1')->orderBy('id','DESC')->get();
        $menu_active = "item|item|0";
        return view('item.item', compact('item','menu_active') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $kategori = Kategori::where('status_aktif','1')->get();
       $menu_active = "item|item|1";
       return view('item.create_item', compact('kategori','menu_active'));
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
        $validator = \Validator::make($req,[ 'nama_item' => 'required|unique:item',
         'kategori' => 'required',
         'harga' => 'required|numeric',
         'margin' => 'required|numeric',
         'gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG',
         'stock' => 'required|numeric',
         'deskripsi' => 'required',
         'kode_item' => 'required'
     ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $req = $request->except('gambar');
        $req['diinput_by'] = Auth::User()->name;
        $req['kategori_id'] = $req['kategori'];
        $req['code'] = $req['kode_item'];

        $insertItem = Item::create($req);

        $gambarUtama = $request->gambar;
        $upload = KompresFoto::UbahUkuran($gambarUtama,'item');
        $insertUpload = GambarItem::create(['item_id' => $insertItem->id, 'gambar' => $upload, 'utama' => '1']);

        if($req['stock'] > 0){
            $insertStock = Stocker::create(['item_id' => $insertItem->id, 
                'jumlah' => $req['stock'], 
                'input_by' => Auth::User()->name ]);
        }

        return redirect()->route('item.index')->with('success','Berhasil Input Item');

    }

    public function store_gambarItem(Request $request)
    {
        $req = $request->all();
        $validator = \Validator::make($req,['gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG']);
        
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','gambar');
        }

        $upload = KompresFoto::UbahUkuran($req['gambar'],'item');
        $Insert = GambarItem::create(['gambar' => $upload, 'utama' => '0', 'item_id' => $req['item_id'] ]);

        return redirect()->back()->with('success_al_gambar','Berhasil Input Gambar')->with('success_tab','gambar');
    }

    public function ganti_gambar_utama($id)
    {
        $sel = GambarItem::where('utama','1')->first();
        $reset = GambarItem::findOrFail($sel->id);
        $reset->update(['utama' => '0']);
        
        $findNew = GambarItem::findOrFail($id);
        $findNew->update(['utama' => '1']);


        return redirect()->back()->with('success_gu_gambar','Berhasil Mengubah Gambar Utama')->with('success_tab','gambar');
    }

    public function hapus_gambar_item($id)
    {
        $find = GambarItem::findOrFail($id);
        if($find->utama == "1"){
            $select = GambarItem::where('id','>',$find['id'])->first();
            $findUp = GambarItem::findOrFail($select->id);
            $findUp->update(['utama' => '1']);
        }
        $hapusFoto = KompresFoto::HapusFoto($find->gambar);
        $find->delete();

        return redirect()->back()->with('success_del_gambar','Berhasil Hapus Gambar Item')->with('success_tab','gambar');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);
        $gambarUtama = $item->GambarItem()->where('utama','1')->first();
        $stocker = Stocker::where('item_id',$id)->orderBy('created_at','DESC')->get();
        $listGambarItem = GambarItem::where(['item_id' => $id])->orderBy('utama','DESC')->get();
        
        $menu_active = "item|item|1";
        return view('item.show_item',compact('item','gambarUtama', 'listGambarItem','stocker','menu_active'));
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
        $validator = \Validator::make($req,['nama_item' => 'required',
            'harga' => 'required|numeric',
            'margin' => 'required|numeric',
            'deskripsi' => 'required',
            'kode_item' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $req['code'] = $req['kode_item'];
        
        $find = Item::findOrFail($id);
        $find->update($req);

        return redirect()->back()->with('success_detail','Berhasil Mengubah Informasi Item');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $find = Item::findOrFail($id);
        $find->delete();
        return redirect()->back()->with('success','Berhasil Hapus Item');
    }
}
