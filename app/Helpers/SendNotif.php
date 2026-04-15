<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Events\PusherEvent;
use App\Notifikasi;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;


class SendNotif{

    private static function sendFcmRequest(array $payload)
    {
        $serverKey = config('fcm.http.server_key');
        $endpoint = config('fcm.http.server_send_url', 'https://fcm.googleapis.com/fcm/send');

        if (empty($serverKey)) {
            return null;
        }

        return Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);
    }

 	public static function sendNotifikasi($token, $pengirim, $pesan, $gambar){

        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $pengirim;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('upload/images-400/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        self::sendFcmRequest([
            'registration_ids' => is_array($token) ? $token : [$token],
            'time_to_live' => 60 * 20,
            'data' => $res,
        ]);
    }


    

    public static function sendTopic($judul,$pesan)
    {
        self::sendFcmRequest([
            'to' => '/topics/global',
            'notification' => [
                'title' => $judul,
                'body' => $pesan,
                'sound' => 'default',
            ],
        ]);
    }

     public static function sendTopicWithData($pengirim,$judul,$pesan,$gambar)
    {
        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $judul;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('upload/images-400/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        self::sendFcmRequest([
            'to' => '/topics/global',
            'time_to_live' => 60 * 20,
            'data' => $res,
        ]);
    }

    public static function sendTopicWithUserId($pengirim,$judul,$pesan,$gambar, $id_user,$namaTable, $id)
    {
        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $judul;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('upload/images-400/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        $res['data']['id'] = $id; // id trx atau id campaign( sesuiakn dengan id)
        $res['data']['table'] = $namaTable;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        self::sendFcmRequest([
            'to' => '/topics/userAgogo'.$id_user,
            'time_to_live' => 60 * 20,
            'data' => $res,
        ]);
    }

    public static function SendNotifPus($pengirim_id,$pengirim_nama,$penerima_id,$judul_id,$judul,$jenis_notif)
    {
        $insert = Notifikasi::create(['pengirim_id' => $pengirim_id,
                                      'penerima_id' => $penerima_id,
                                      'judul_id' => $judul_id,
                                      'judul' =>$judul,
                                      'isi' => $judul,
                                      'jenis_notif' => $jenis_notif,
                                      'dibaca' => '0'
                                    ]);
        event(new PusherEvent('1',$pengirim_nama." , ".$judul, [$penerima_id]));

    }

    public static function SendNotPesan($type,$message,$userID = null){
        event(new PusherEvent($type,$message,$userID));
    }
    
    public  function simpanNotif()
    {
        return "insert Notif Db";
    }

    public static function sendNotifWa($no_hp, $message)
    {
        // $waktu_skrang = Carbon::now();
        $response = "";
        $response = Curl::to('http://localhost/web_gw/api/post.php')
            ->withData(
                array(
                    'Phone' => $no_hp,
                    'Message' =>  $message,
                    'Apikey' => '3823'
                )
            )
            ->post();

            // $response = Curl::to('https://ampel.wablas.com/api/send-message')
            //             ->withData( array( 'phone' => $no_hp,'message' => $message) )
            //             ->withHeader('Authorization: c3JhyjSlbIknDJN1PTUx3KiQZxgbGaNOdtZEPYUyuBt43OQfJtfg2zOlSxZCeVRo')
            //             ->asJson( true )
            //             ->post();
        return $response;
    }


    
    public static function kirimEmail($email,$data,$subject)
    {
        //kirim email 
        Mail::send('email_template',$data,function ($mail) use ($email,$subject) {
                $mail->to($email,'no-reply')
                     ->subject($subject);
                $mail->from('agogodevelop@gmail.com','AgogoBakery.com');
        });

        if(Mail::failures()){
            return "Gagal Kirim Email";
        }else{
            return "sukses";
        }
    }

    
}
