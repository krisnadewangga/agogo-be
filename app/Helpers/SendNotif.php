<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Mail;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use App\Events\PusherEvent;
use App\Notifikasi;
use FCM;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;


class SendNotif{

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

	    $optionBuiler = new OptionsBuilder();
	    $optionBuiler->setTimeToLive(60*20);

	    $notificationBuilder = new PayloadNotificationBuilder($pengirim);
	    $notificationBuilder->setBody($pesan)->setSound('default');

	    $dataBuilder = new PayloadDataBuilder();
	     $dataBuilder->addData($res);

	    $option = $optionBuiler->build();
	    $notification = $notificationBuilder->build();
	    $data = $dataBuilder->build();

	    $downstreamResponse = FCM::sendTo($token, $option, null, $data);
    }

    public static function sendTopic($judul,$pesan)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

        $notification = $notificationBuilder->build();

        $topic = new Topics();
        $topic->topic('global');

        $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

     public static function sendTopicWithData($pengirim,$judul,$pesan,$gambar)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

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
        $topic = new Topics();
        $topic->topic('global');


        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($pengirim);
        $notificationBuilder->setBody($pesan)->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
         $dataBuilder->addData($res);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $topicResponse = FCM::sendToTopic($topic, $option, null, $data);


        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

    public static function sendTopicWithUserId($pengirim,$judul,$pesan,$gambar, $id_user,$namaTable, $id)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

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



        $topic = new Topics();
	    // ganti topic(user
        $topic->topic('userAgogo'.$id_user);


        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($pengirim);
        $notificationBuilder->setBody($pesan)->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
         $dataBuilder->addData($res);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $topicResponse = FCM::sendToTopic($topic, $option, null, $data);


        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
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
