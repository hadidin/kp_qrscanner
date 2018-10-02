<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class maxpark_scanner_cont extends Controller
{
    //
    public function vendorscannersimulator(){

      $client = new \GuzzleHttp\Client([
         'verify' => false
       ]);
       $user_qr=$_GET["user_qr"];
       // echo "raw from receive=".$user_qr."<br>";
       $user_qr=str_replace(" ","+",$user_qr);
       // echo $user_qr;
       // echo "<br><br><br>";
       $odata = array('userdata' => $user_qr);
       // echo "after convert array=";
       // print_r($odata);
       // echo "<br>";

       $odata=json_encode($odata,JSON_UNESCAPED_SLASHES);
       // print_r($odata);
       echo "after convert to json =";
       print_r($odata);
       echo "<br>";

       $body = \GuzzleHttp\Psr7\stream_for($odata);
       $url=config('custom.kp_url')."/api/user/validate";
       Log::info("xxx $url ^ $odata");
       try{
         $res = $client->request('POST', $url, ['body' => $body, 'headers'  => [
           'Content-Type' => 'application/json']]);
         $data = $res->getBody();
         $response=json_decode($data);
         $is_valid=$response->is_valid;
         #insertTicket
         if($is_valid==1){
           self::apitoinsertticket($user_qr,"123456");
           echo "go to insert ticket";
         }
         else{
           dd($response);
         }


         //print_r($data);
       }
       catch(\Exception $e){
         $kx=$e->getResponse()->getBody();
         $k = json_decode($kx);
         dd($k);
       }
    }

    public static function apitoinsertticket($user_qr,$ticket_id){
      $client = new \GuzzleHttp\Client([
         'verify' => false
       ]);
       // $user_qr=$_GET["user_qr"];
       $odata = array(
                      'userdata' => $user_qr,
                      'site_id' => config('custom.site_id'),
                      'ticket_id' => config('custom.ticket_id')
                    );
       $odata=json_encode($odata,JSON_UNESCAPED_SLASHES);
       // echo "sasassas";
       print_r($odata);
       $body = \GuzzleHttp\Psr7\stream_for($odata);
       $url=config('custom.kp_url')."/api/vendor/insert_ticket";
       $accessToken=config('custom.access_token');
       Log::info("insert ticket $url ^ $odata");
       try{
         $res = $client->request('POST', $url, ['body' => $body, 'headers'  => [
              'Content-Type' => 'application/json','Accept' => 'application/json','Authorization' => 'Bearer ' . $accessToken]]);
         $data = $res->getBody();
         $character = json_decode($data);
         print_r($character);
         echo  "barier open";

         #insertTicket


         //print_r($data);
       }
       catch(\Exception $e){
         Log::error("insert ticket $e");
         $kx=$e->getResponse()->getBody();
         $k = json_decode($kx);
         dd($k);

       }


    }
}
