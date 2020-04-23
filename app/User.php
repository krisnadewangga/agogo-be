<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DetailKonsumen;
use App\Role;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use Notifiable, HasApiTokens, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'level_id','name', 'email','no_hp','password','foto','status_aktif'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected $appends = array('ket_tgl_lahir');
    

    public function getKetTglLahirAttribute()
    {
      $sel_tglLahir = DetailKonsumen::where('id',$this->id)->select('tgl_lahir')->first();
      if(!empty($sel_tglLahir->tgl_lahir) ){
        $tgl_lahir = $sel_tglLahir->tgl_lahir->format('d M Y');
      }else{
        $tgl_lahir = "<label class='label label-warning'>Belum Ditentukan</label>";
      }

      return $tgl_lahir;
    }

    // public function getLevelAttribute(){
    //     $level = Role::where('user_id',$this->id)->select('level_id')->first();
    //     return $level;
    // }


  
    public function Level()
    {
        return $this->belongsTo(Level::class);
    }


   public function Roles()
   {
     return $this->hasMany(Role::class);
   }


    public function DetailKonsumen()
    {
        return $this->hasOne(DetailKonsumen::class);
    }

    public function Transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }


    public function HistoriTopup()
    {
        return $this->hasMany(HistoriTopup::class);
    }
    
    public function LogBan()
    {
        return $this->hasMany(LogBan::class);
    }

    public function Pesan()
    {
        return $this->hasMany(Pesan::class);
    }
    
    public function Kas()
    {
      return $this->hasMany(Kas::class);
    }

    
}
