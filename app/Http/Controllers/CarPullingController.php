<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class CarPullingController extends Controller
{
   public function index(){

      return view('CarPulling/car_pulling');

   } 
   public function car_pooling_report(){

      $sql = "select * from vehicle_registration order by vr_entry_datetime desc";
      $data = DB::select($sql);
      return view('CarPulling/car_pulling_report',compact('data'));

   } 
   public function insertRequest(Request $request){

     
      if($request->get('pay_for_one_side') == 'One Way' || $request->get('pay_for_one_side') == 'Two Way'){
         if($request->get('Monday_morning_time') != '' && $request->get('Monday_back_time') != ''){
            // echo $request->get('Monday_morning_time').'-'.$request->get('Monday_back_time').'<br>';
            $time_str = $request->get('Monday_morning_time');
            
            list($hours, $minutes) = explode(":", $time_str);
            $mondayMoringTimeNo = ($hours * 60) + $minutes;     
            $time_str = $request->get('Monday_back_time');
            list($hours, $minutes) = explode(":", $time_str);
            $mondayBackTimeNo = ($hours * 60) + $minutes;

            if($mondayMoringTimeNo >= $mondayBackTimeNo){        
               return response()->json(array('message'=>'Monday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Tuesday_morning_time') != '' && $request->get('Tuesday_back_time') != ''){
            $Tues_time_str = $request->get('Tuesday_morning_time');
            list($hours, $minutes) = explode(":", $Tues_time_str);
            $tuesdayMoringTimeNo = ($hours * 60) + $minutes;      
            $Tues_time_str = $request->get('Tuesday_back_time');
            list($hours, $minutes) = explode(":", $Tues_time_str);
            $tuesdayBackTimeNo = ($hours * 60) + $minutes;

            if($tuesdayMoringTimeNo >= $tuesdayBackTimeNo){        
               return response()->json(array('message'=>'Tuesday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Wednesday_morning_time') != '' && $request->get('Wednesday_back_time') != ''){
           
            $Wed_time_str = $request->get('Wednesday_morning_time');        
            list($hours, $minutes) = explode(":", $Wed_time_str);
            $wedMoringTimeNo = ($hours * 60) + $minutes;      
            $Wed_time_str = $request->get('Wednesday_back_time');
            list($hours, $minutes) = explode(":", $Wed_time_str);
            $wedBackTimeNo = ($hours * 60) + $minutes;
               
            if($wedMoringTimeNo >= $wedBackTimeNo){        
               return response()->json(array('message'=>'Wednesday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Thursday_morning_time') != '' && $request->get('Thursday_back_time') != ''){
            $Thurs_time_str = $request->get('Thursday_morning_time');
            list($hours, $minutes) = explode(":", $Thurs_time_str);
            $thursMoringTimeNo = ($hours * 60) + $minutes;      
            $Thurs_time_str = $request->get('Thursday_back_time');
            list($hours, $minutes) = explode(":", $Thurs_time_str);
            $thursBackTimeNo = ($hours * 60) + $minutes;    
         
            if($thursMoringTimeNo >= $thursBackTimeNo){        
               return response()->json(array('message'=>'Thursday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Friday_morning_time') != '' && $request->get('Friday_back_time') != ''){
            $friday_time_str = $request->get('Friday_morning_time');
            list($hours, $minutes) = explode(":", $friday_time_str);
            $fridayMoringTimeNo = ($hours * 60) + $minutes;      
            $friday_time_str = $request->get('Friday_back_time');
            list($hours, $minutes) = explode(":", $friday_time_str);
            $fridayBackTimeNo = ($hours * 60) + $minutes;    
         
            if($fridayMoringTimeNo >= $fridayBackTimeNo){        
               return response()->json(array('message'=>'Friday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Saturday_morning_time') != '' && $request->get('Saturday_back_time') != ''){
            $sat_time_str = $request->get('Saturday_morning_time');
            list($hours, $minutes) = explode(":", $sat_time_str);
            $satMoringTimeNo = ($hours * 60) + $minutes;      
            $sat_time_str = $request->get('Saturday_back_time');
            list($hours, $minutes) = explode(":", $sat_time_str);
            $satBackTimeNo = ($hours * 60) + $minutes;    
         
            if($satMoringTimeNo >= $satBackTimeNo){        
               return response()->json(array('message'=>'Saturday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Sunday_morning_time') != '' && $request->get('Sunday_back_time') != ''){
            $sun_time_str = $request->get('Sunday_morning_time');
            list($hours, $minutes) = explode(":", $sun_time_str);
            $sunMoringTimeNo = ($hours * 60) + $minutes;      
            $sun_time_str = $request->get('Sunday_back_time');
            list($hours, $minutes) = explode(":", $sun_time_str);
            $sunBackTimeNo = ($hours * 60) + $minutes;    
         
            if($sunMoringTimeNo >= $sunBackTimeNo){        
               return response()->json(array('message'=>'Sunday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }
      }
      //echo "done";exit;
      // return response()->json(array('data'=>'Done','status'=>false));
      $want_ride        = $request->get('want_ride');
      $contact_me       = $request->get('contact_me');      
      $search_data      = $request->get('search_data');      
      $vehical_type     = $request->get('vehical_type'); 
      $share_with       = $request->get('share_with'); 
      $share_info       = $request->get('share_info'); 
      $route_share_info = $request->get('route_share_info'); 
      $vehicle          = $request->get('vehicle');
      $vehicle_model    = $request->get('vehicle_model');
      $vehicle_capacity = $request->get('vehicle_capacity');
      $vehicle_no       = $request->get('vehicle_no');
      $area_pickup      = $request->get('area_pickup');
      $monday           = $request->get('Monday_morning_time').'-'.$request->get('Monday_back_time');      
      $tuesday          = $request->get('Tuesday_morning_time').'-'.$request->get('Tuesday_back_time');
      $wednesday        = $request->get('Wednesday_morning_time').'-'.$request->get('Wednesday_back_time');  
      $thursday         = $request->get('Thursday_morning_time').'-'.$request->get('Thursday_back_time');        
      $friday           = $request->get('Friday_morning_time').'-'.$request->get('Friday_back_time');     
      $saturday         = $request->get('Saturday_morning_time').'-'.$request->get('Saturday_back_time');     
      $sunday           = $request->get('Sunday_morning_time').'-'.$request->get('Sunday_back_time');    
      $route_link       = $request->get('route_link');
      $negotitate       = $request->get('negotiate'); 
      $uni_timings      = $request->get('uni_timings');  
      $smoking_info     = $request->get('smoking_info');  
      $pay_for_one_side = $request->get('pay_for_one_side'); 
      $specific_date_time = $request->get('specific_date_time');  
      $contact_detail = $request->get('contact_detail');  
      $pay_for_one_side_time = $request->get('time');    
      $username         =$request->get('username');
      
      $inserCarPullingSql = DB::table('car_pooling')->insert(
         [
         'CP_WANT_RIDE'       => $want_ride,
         'CP_CONTACT_ME'      => $contact_me ,
         'CP_CONTACT_DET'      => $contact_detail ,
         'CP_PREFERRED_LOCATION'  => $search_data ,
         'CP_VEHICAL_TYPE'        => $vehical_type ,
         'CP_FURTHER_INFORMATION' => $share_info ,
         'CP_ROUTE_INFORMATION'  => $route_share_info ,
         'CP_ROUTE_OPTION'    => $pay_for_one_side ,
         'CP_TIMINGS'         => $uni_timings ,
         'CP_SHARE_WITH'      => $share_with ,
         'CP_VEHICAL_CC'      => $vehicle ,
         'CP_VEHICAL_MODEL'   => $vehicle_model,
         'CP_CAPACITY'        => $vehicle_capacity,
         'CP_VEHICAL_NO'        => $vehicle_no,
         'CP_AREA_PICKUP'     => $area_pickup,        
         'CP_MONDAY'          => $monday,
         'CP_TUESDAY'         => $tuesday,
         'CP_WEDNESDAY'       => $wednesday,
         'CP_THURSDAY'        => $thursday,
         'CP_FRIDAY'          => $friday,
         'CP_SATURDAY'        => $saturday,
         'CP_SUNDAY'          => $sunday,         
         'CP_PAY_FOR_ONESIDE' => $pay_for_one_side_time,
         'CP_SPECIFIC_DAY'    => $specific_date_time,
         'CP_SMOKING'         => $smoking_info,
         'CP_NEGOTIATE'       => $negotitate,
         'CP_ROUTE_LINK'      => $route_link,
         'CP_ENTRY_USERNAME'  => $username        
         ]
       );
      return response()->json(array('data'=>$inserCarPullingSql,'status'=>true));
   }

      public function deleteCarPooling(Request $request){
         $partner = $request->input('cp_partner');
         $entry_username = $request->input('cp_entry_username');
         
         //  dd( $partner, $entry_username);

         try {
            // 1️⃣ Delete records where partner was the one offering or getting a ride
            DB::table('car_pooling')
                  ->where('cp_entry_username', $partner)
                  ->whereNotIn('cp_want_ride', ['Offer Ride', 'Get Ride'])
                  ->delete();

            // 2️⃣ Clear partner info for the same partner (Offer/Get Ride)
            DB::table('car_pooling')
                  ->where('cp_entry_username', $partner)
                  ->whereIn('cp_want_ride', ['Offer Ride', 'Get Ride'])
                  ->update(['cp_partner' => '']);

            // 3️⃣ Clear partner info for the entry user (Offer/Get Ride)
            DB::table('car_pooling')
                  ->where('cp_entry_username', $entry_username)
                  ->whereIn('cp_want_ride', ['Offer Ride', 'Get Ride'])
                  ->update(['cp_partner' => '']);

            // 4️⃣ Delete BE_SHARED record for the entry user
            DB::table('car_pooling')
                  ->where('cp_entry_username', $entry_username)
                  ->whereIn('cp_want_ride', ['BE_SHARED'])
                  ->delete();

            // 5️⃣ Delete PENDING record where entry user is the partner
            DB::table('car_pooling')
                  ->where('cp_partner', $entry_username)
                  ->whereIn('cp_want_ride', ['PENDING'])
                  ->delete();

            return response()->json(['message' => 'success']);
         } catch (\Exception $e) {
            return response()->json([
                  'message' => 'error',
                  'error' => $e->getMessage()
            ], 500);
         }
      }

   public function updateRequest(Request $request){
      if($request->get('pay_for_one_side') == 'One Way' || $request->get('pay_for_one_side') == 'Two Way'){
         if($request->get('Monday_morning_time') != '' && $request->get('Monday_back_time') != ''){
            // echo $request->get('Monday_morning_time').'-'.$request->get('Monday_back_time').'<br>';
            $time_str = $request->get('Monday_morning_time');
            
            list($hours, $minutes) = explode(":", $time_str);
            $mondayMoringTimeNo = ($hours * 60) + $minutes;     
            $time_str = $request->get('Monday_back_time');
            list($hours, $minutes) = explode(":", $time_str);
            $mondayBackTimeNo = ($hours * 60) + $minutes;

            if($mondayMoringTimeNo >= $mondayBackTimeNo){        
               return response()->json(array('message'=>'Monday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Tuesday_morning_time') != '' && $request->get('Tuesday_back_time') != ''){
            $Tues_time_str = $request->get('Tuesday_morning_time');
            list($hours, $minutes) = explode(":", $Tues_time_str);
            $tuesdayMoringTimeNo = ($hours * 60) + $minutes;      
            $Tues_time_str = $request->get('Tuesday_back_time');
            list($hours, $minutes) = explode(":", $Tues_time_str);
            $tuesdayBackTimeNo = ($hours * 60) + $minutes;

            if($tuesdayMoringTimeNo >= $tuesdayBackTimeNo){        
               return response()->json(array('message'=>'Tuesday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Wednesday_morning_time') != '' && $request->get('Wednesday_back_time') != ''){
           
            $Wed_time_str = $request->get('Wednesday_morning_time');        
            list($hours, $minutes) = explode(":", $Wed_time_str);
            $wedMoringTimeNo = ($hours * 60) + $minutes;      
            $Wed_time_str = $request->get('Wednesday_back_time');
            list($hours, $minutes) = explode(":", $Wed_time_str);
            $wedBackTimeNo = ($hours * 60) + $minutes;
               
            if($wedMoringTimeNo >= $wedBackTimeNo){        
               return response()->json(array('message'=>'Wednesday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Thursday_morning_time') != '' && $request->get('Thursday_back_time') != ''){
            $Thurs_time_str = $request->get('Thursday_morning_time');
            list($hours, $minutes) = explode(":", $Thurs_time_str);
            $thursMoringTimeNo = ($hours * 60) + $minutes;      
            $Thurs_time_str = $request->get('Thursday_back_time');
            list($hours, $minutes) = explode(":", $Thurs_time_str);
            $thursBackTimeNo = ($hours * 60) + $minutes;    
         
            if($thursMoringTimeNo >= $thursBackTimeNo){        
               return response()->json(array('message'=>'Thursday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Friday_morning_time') != '' && $request->get('Friday_back_time') != ''){
            $friday_time_str = $request->get('Friday_morning_time');
            list($hours, $minutes) = explode(":", $friday_time_str);
            $fridayMoringTimeNo = ($hours * 60) + $minutes;      
            $friday_time_str = $request->get('Friday_back_time');
            list($hours, $minutes) = explode(":", $friday_time_str);
            $fridayBackTimeNo = ($hours * 60) + $minutes;    
         
            if($fridayMoringTimeNo >= $fridayBackTimeNo){        
               return response()->json(array('message'=>'Friday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Saturday_morning_time') != '' && $request->get('Saturday_back_time') != ''){
            $sat_time_str = $request->get('Saturday_morning_time');
            list($hours, $minutes) = explode(":", $sat_time_str);
            $satMoringTimeNo = ($hours * 60) + $minutes;      
            $sat_time_str = $request->get('Saturday_back_time');
            list($hours, $minutes) = explode(":", $sat_time_str);
            $satBackTimeNo = ($hours * 60) + $minutes;    
         
            if($satMoringTimeNo >= $satBackTimeNo){        
               return response()->json(array('message'=>'Saturday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }

         if($request->get('Sunday_morning_time') != '' && $request->get('Sunday_back_time') != ''){
            $sun_time_str = $request->get('Sunday_morning_time');
            list($hours, $minutes) = explode(":", $sun_time_str);
            $sunMoringTimeNo = ($hours * 60) + $minutes;      
            $sun_time_str = $request->get('Sunday_back_time');
            list($hours, $minutes) = explode(":", $sun_time_str);
            $sunBackTimeNo = ($hours * 60) + $minutes;    
         
            if($sunMoringTimeNo >= $sunBackTimeNo){        
               return response()->json(array('message'=>'Sunday Morning Time cannot be Equal or Less then Then Back Time','status'=>false));
            }
         }
      }
      $cp_id            = $request->get('cp_id');
      $want_ride        = $request->get('want_ride');
      $contact_me       = $request->get('contact_me');      
      $search_data      = $request->get('search_data');      
      $vehical_type     = $request->get('vehical_type'); 
      $share_with       = $request->get('share_with'); 
      $share_info       = $request->get('share_info'); 
      $route_share_info = $request->get('route_share_info'); 
      $vehicle          = $request->get('vehicle');
      $vehicle_model    = $request->get('vehicle_model');
      $vehicle_capacity = $request->get('vehicle_capacity');
      $vehicle_no       = $request->get('vehicle_no');
      $area_pickup      = $request->get('area_pickup');
      $monday           = $request->get('Monday_morning_time').'-'.$request->get('Monday_back_time');      
      $tuesday          = $request->get('Tuesday_morning_time').'-'.$request->get('Tuesday_back_time');
      $wednesday        = $request->get('Wednesday_morning_time').'-'.$request->get('Wednesday_back_time');  
      $thursday         = $request->get('Thursday_morning_time').'-'.$request->get('Thursday_back_time');        
      $friday           = $request->get('Friday_morning_time').'-'.$request->get('Friday_back_time');     
      $saturday         = $request->get('Saturday_morning_time').'-'.$request->get('Saturday_back_time');     
      $sunday           = $request->get('Sunday_morning_time').'-'.$request->get('Sunday_back_time');    
      $route_link       = $request->get('route_link');
      $negotitate       = $request->get('negotiate'); 
      $uni_timings      = $request->get('uni_timings'); 
      $smoking_info     = $request->get('smoking_info');   
      $pay_for_one_side = $request->get('pay_for_one_side');  
      $contact_detail = $request->get('contact_detail');  
      $specific_date_time = $request->get('specific_date_time');  
      $pay_for_one_side_time = $request->get('time');    
      $username         =  $request->get('username');

      $update = "update car_pooling set
                  cp_want_ride             = '$want_ride',
                  cp_contact_me            = '$contact_me',
                  cp_contact_det           = '$contact_detail',
                  cp_further_information   = '$share_info',
                  cp_route_information     = '$route_share_info',
                  cp_route_option          = '$pay_for_one_side',
                  cp_preferred_location    = '$search_data',
                  cp_timings               = '$uni_timings',
                  cp_vehical_type          = '$vehical_type',
                  cp_share_with            = '$share_with',
                  cp_vehical_cc            = '$vehicle',
                  cp_vehical_model         = '$vehicle_model',
                  cp_capacity              = '$vehicle_capacity',
                  cp_vehical_no            = '$vehicle_no',
                  cp_area_pickup           = '$area_pickup',
                  cp_monday                = '$monday',
                  cp_tuesday               = '$tuesday',
                  cp_wednesday             = '$wednesday',
                  cp_thursday              = '$thursday',
                  cp_friday                = '$friday',
                  cp_saturday              = '$saturday',
                  cp_sunday                = '$sunday',
                  cp_smoking               = '$smoking_info',
                  cp_specific_day          = '$specific_date_time',
                  cp_pay_for_oneside       = '$pay_for_one_side_time',
                  cp_route_link            = '$route_link',
                  cp_entry_username        = '$username'
                  where cp_id = $cp_id ";
                 //echo $update;exit;
      $res = DB::insert($update);
      return response()->json(array('data'=>$res,'status'=>true));
   }

   public function getRequest(){

      $sql = "select * from car_pooling";
      $res = DB::select($sql);
      // return response()->json(array('status' => true,'data'=> [], 'message' => $action.' Starts Successfully',
      return response()->json(array('data'=>$res,'status'=>true));

   }

   
   public function pooling_request_aceept_cancel(Request $request){
    //dd($request->all());
      $request_user = $request->get('requested_user');
     // dd($request_user);
     if($request->get('action')=='delete'){
         //dd($request->all());
         $delcap = "delete from car_pooling where  cp_id = ".$request->get('cp_id')." ";
         $res=DB::delete($delcap);
         $remove_partner = $request->get('requested_user');
         $updCapDell = "update car_pooling 
         set  cp_partner = replace(CP_PARTNER,'$remove_partner','')  
         where CP_WANT_RIDE not in  ('BE_SHARED')
         and cp_entry_username  = '".$request->get('user_ID')."'
         ";
         $res=DB::insert($updCapDell);
         if($request->get('ridestatus') != 0){
            $carPoolingCapacitySql = "select * from car_pooling where cp_entry_username = '".$request->get('user_ID')."' and cp_want_ride in ('Offer Ride')";
            $rescarPoolingCapacitySql = DB::select($carPoolingCapacitySql);
            
            $cap   = $rescarPoolingCapacitySql[0]->cp_capacity+1;//$request->get('cap')+1;
            $updCap = "update car_pooling set cp_capacity = $cap where cp_entry_username = '".$request->get('user_ID')."' and cp_want_ride in ('Offer Ride')";
            //echo $updCap;
            //dd($request->all());
            $resupdCap=DB::insert($updCap);
         }
         // if($resupdCap){
         $to = strtolower($request->get('user_ID')).'@bnu.edu.pk';
         $subject = 'Car Pooling Request Cancelled by '.$request_user;
         $message =  'Car Pooling Accepted Request has been cancelled by Client '.$request_user;
         $this->send_mail($to,$subject,$message);
         // }
    }else{
    
         $checkRequestStatus = "select * from car_pooling where cp_partner = '".$request->get('requested_user')."' and cp_want_ride in ('BE_SHARED')";

         

         $rescheckRequestStatus = DB::select($checkRequestStatus);

         if(count($rescheckRequestStatus)>0){
            return response()->json(array('data'=>'Duplicate row not allowed','status'=>true));
         }else{
         
            //dd($request->all());
            $inserCarPullingSql = DB::table('car_pooling')->insert(
               [
               'CP_WANT_RIDE'       => 'PENDING',
               'CP_PARTNER'         => $request_user,
               'PARENT_CP_ID'       => $request->get('cp_id'),
               'CP_ENTRY_USERNAME'  => $request->get('user_ID')        
               ]
            );
            if($inserCarPullingSql){
               $to = strtolower($request->get('user_ID')).'@bnu.edu.pk';
               $subject = 'Car Pooling Request from '.$request_user;
               $message =  $request_user. ' has sent you a Car Pooling Request, kindly check Rides Request in your CMS Portal';
               $this->send_mail($to,$subject,$message);
            }
         }
      } 
    
      return response()->json(array('data'=>'Done','status'=>true));
    
   }
   public function getRequest2(Request $request){
      $username = $request->get('username');
      $sql = "select * from car_pooling where CP_ENTRY_USERNAME = '$username' and cp_want_ride not in ('BE_SHARED','PENDING') order by cp_id desc";
      $res = DB::select($sql);
      // return response()->json(array('status' => true,'data'=> [], 'message' => $action.' Starts Successfully',
      return response()->json(array('data'=>$res,'status'=>true));


   }

   public function car_pulling_details(Request $request){


      $check_records = "SELECT
                           cp_want_ride
                        FROM
                           car_pooling cpt
                        WHERE
                              cpt.cp_entry_username = '".auth()->user()->username."'
                        and      cp_want_ride  IN (
                                    'Offer Ride','Get Ride'
                              )
                        ";
         $obj_records = DB::select($check_records);    
         
         $var_cp_want_ride = @$obj_records[0]->cp_want_ride;

       $all_query = "select 
       (
         SELECT
             cp_id
         FROM
             car_pooling cpt
         WHERE
                 cpt.cp_partner = '".auth()->user()->username."'
             AND cpt.cp_entry_username = car_pooling.cp_entry_username
      and cp_want_ride not in ('Offer Ride','Get Ride')
      ) AS offer_status,(select count(*) from car_pooling where cp_partner = '".auth()->user()->username."' and cp_want_ride = 'BE_SHARED') as acceptedRequest,
      car_pooling.* from car_pooling 
      where cp_entry_username  not in ('".auth()->user()->username."')
      and cp_share_with not in ('Nobody')";

      if($var_cp_want_ride=="Offer Ride"){
         $all_query.= "and cp_want_ride  in ('Get Ride')";
      }else{
         $all_query.= "and cp_want_ride  in ('Offer Ride')";
      }

      // }elseif($var_cp_want_ride=="Get Ride"){
      //    $all_query.= "and cp_want_ride  in ('Offer Ride')";
      // }
      
      $all_query.= " order by cp_entry_datetime desc ";

//echo $all_query;exit;
   // '".auth()->user()->username."')
      $car_pulling=DB::select($all_query);
      $sql = "SELECT
               distinct entry_user_id,cp_id,car_pooling.cp_want_ride,car_pooling.cp_preferred_location,cp_capacity,cp_contact_det,parent_cp_id
               
            FROM
               messages_bnu
               left join car_pooling on cp_id = to_number(parent_message_id)
            WHERE
                  type = 'car_pulling'
               AND cp_entry_username='".auth()->user()->username."'                                                                   
               and entry_user_id not in ('".auth()->user()->username."')
      ";
      //echo $sql;exit();
      $res = DB::select($sql);
      
      return view('CarPulling/car_pulling_details',['car_pulling'=>$car_pulling,'car_pulling_count'=>$res,'total_inbox'=>$this->chat_count(),'total_chats'=>$this->carpullingInboxchatcount(),'cp_want_ride'=>$var_cp_want_ride]);

   }

   

   public function sendChat(Request $request){ 

      $username         = $request->get('authUsername');
      $message          = $request->get('message');
      $type             = 'car_pulling';
      $car_pulling_id   = $request->get('cp_id');
      $to_user          = $request->get('sendToUsername');
      $time = Carbon::now();
      $inserCarPullingSql = DB::table('messages_bnu')->insert(
         [
         'entry_user_id'      => $username,
         'to_user_id'         => $to_user,
         'CREATED_AT'         => $time,
         'MESSAGE'            => $message,
         'MESSAGE_TYPE'       => $car_pulling_id,
         'type'               => $type,
         'parent_message_id'  => $request->get('cp_id')
         ]
       );

      $insertNotification ="INSERT INTO user_notifications (
         un_description,
         un_to_user_id,     
         target_page
      ) VALUES (
         'Car Pooling Request Notification',
         '$to_user',       
         'carpooling'
      )";
      $res = DB::insert($insertNotification);
      return response()->json(array('data'=>$inserCarPullingSql,'status'=>true));
   }  
   public function getMessage(Request $request){
 //dd($request->all());
      $cp_id          = $request->get('cp_id');
       $sql = "SELECT
                     id,
                     entry_user_id,
                     created_at,
                     to_user_id,
                     message,
                     message_type,
                     type
               FROM
                     (
                        SELECT
                        id,
                           entry_user_id,
                           created_at,
                           to_user_id,
                           message,
                           message_type,
                           type
                        FROM
                           messages_bnu
                        WHERE
                                 type = 'car_pulling'
                           AND
                                 message_type = $cp_id 
                           and (
                                 (entry_user_id = '".$request->get('authUsername')."'  and to_user_id = '".auth()->user()->username."')
                                 or
                                 (to_user_id = '".$request->get('authUsername')."'  and entry_user_id = '".auth()->user()->username."')
                              )      
                     ";

      if($request->get('username')!="all"){
         // $sql.= " AND entry_user_id = '".$request->get('username')."'  "; 
         $sql.= " AND (entry_user_id = '".$request->get('username')."' or to_user_id = '".$request->get('username')."' ) ";
      }
      $sql.= "             
            )
      ORDER BY created_at desc";
     //echo $sql;exit();
      $res = DB::select($sql);      
      // return count($count) = DB::select($sql)->num_rows ;
      return response()->json(array('data'=>$res,'status'=>true));
   }
   public function carpullingInbox(Request $request){
             $sql = "SELECT
            cp_entry_username AS entry_user_id,
            cp_entry_username,
            cp_id,
            car_pooling.cp_want_ride,
            car_pooling.cp_preferred_location,
            cp_capacity,
            case when cp_entry_username='".auth()->user()->username."' then cp_partner else cp_entry_username end as cp_partner,
            case when cp_entry_username='".auth()->user()->username."' then 'Remove by Owner' ELSE 'Cancel My Request' end AS remove_status,
            parent_cp_id,
            (select cp_capacity cpc from car_pooling cpc where cpc.cp_id =  car_pooling.parent_cp_id) as remaining_capacity
            FROM car_pooling
            WHERE car_pooling.cp_want_ride IN ('PENDING','BE_SHARED')
            AND (cp_entry_username = '".auth()->user()->username."' OR cp_partner = '".auth()->user()->username."')";
//echo $sql;exit;
      $res = DB::select($sql);

      return view('CarPulling/carpullingInbox', ['car_pulling'=>$res, 'total_inbox'=>$this->chat_count(), 'total_chats'=>$this->carpullingInboxchatcount()]);

   }
   public function carpullingInboxchat(Request $request){
      $cp_id          = $request->get('cp_id');


      $sql = "
      select 
    entry_user_id,
    cp_id,
    cp_want_ride,
    cp_preferred_location,
    cp_capacity,
    cp_partner,
    parent_cp_id
    from (
      SELECT
            distinct entry_user_id,cp_id,
            car_pooling.cp_want_ride,
            car_pooling.cp_preferred_location,
            cp_capacity,
            cp_partner,
            parent_cp_id
         FROM
            messages_bnu
            left join car_pooling on cp_id = to_number(parent_message_id)
         WHERE
                type = 'car_pulling'
                and (messages_bnu.entry_user_id = '".auth()->user()->username."' or to_user_id = '".auth()->user()->username."')
            union 
      SELECT
            distinct entry_user_id,cp_id,
            car_pooling.cp_want_ride,
            car_pooling.cp_preferred_location,
            cp_capacity,
            cp_partner,
            parent_cp_id
         FROM
            messages_bnu
            left join car_pooling on cp_id = to_number(parent_message_id)
         WHERE
                type = 'car_pulling'
                and cp_id is not null
            AND (entry_user_id='".auth()->user()->username."'  or car_pooling.cp_entry_username='".auth()->user()->username."')
     )
     where entry_user_id not in ('".auth()->user()->username."' )            
      ";
     // echo $sql;
       $res = DB::select($sql);
      // return $res;     
      return view('CarPulling/car_pulling_chat',['car_pulling'=>$res,'total_inbox'=>$this->chat_count(),'total_chats'=>$this->carpullingInboxchatcount()]);
     // return response()->json(array('data'=>$res,'status'=>true));
   }
   public function carpullingInboxchatcount(){
       $sql = "
      select 
    entry_user_id,
    cp_id,
    cp_want_ride,
    cp_preferred_location,
    cp_capacity,
    cp_partner,
    parent_cp_id
    from (
      SELECT
            distinct entry_user_id,cp_id,
            car_pooling.cp_want_ride,
            car_pooling.cp_preferred_location,
            cp_capacity,
            cp_partner,
            parent_cp_id
         FROM
            messages_bnu
            left join car_pooling on cp_id = to_number(parent_message_id)
         WHERE
                type = 'car_pulling'
                and (messages_bnu.entry_user_id = '".auth()->user()->username."' or to_user_id = '".auth()->user()->username."')
            union 
      SELECT
            distinct entry_user_id,cp_id,
            car_pooling.cp_want_ride,
            car_pooling.cp_preferred_location,
            cp_capacity,
            cp_partner,
            parent_cp_id
         FROM
            messages_bnu
            left join car_pooling on cp_id = to_number(parent_message_id)
         WHERE
                type = 'car_pulling'
                and cp_id is not null
            AND (entry_user_id='".auth()->user()->username."'  or car_pooling.cp_entry_username='".auth()->user()->username."')
     )
     where entry_user_id not in ('".auth()->user()->username."' )            
      ";
       $res = DB::select($sql);
       return count($res);
   }
   function chat_count(){
      $sql = "
            
  SELECT
  cp_entry_username AS entry_user_id,
  cp_id,
  car_pooling.cp_want_ride,
  car_pooling.cp_preferred_location,
  cp_capacity,
  cp_partner,
  parent_cp_id
FROM
  car_pooling
WHERE
      car_pooling.cp_want_ride in ('PENDING','BE_SHARED')
      AND (cp_entry_username = '".auth()->user()->username."' or cp_partner = '".auth()->user()->username."' )
      ";
      $res = DB::select($sql);
      return count($res);
   }

   function editCarPull(Request $request){
      $cp_id   = $request->get('cp_id');
      $sql = "SELECT
            (SELECT
         count(*)
      FROM
            car_pooling cpt
      WHERE
            cpt.cp_want_ride IN (
               'PENDING','BE_SHARED'
            )
            and (cpt.cp_entry_username =cp.cp_entry_username or cpt.cp_partner = cp.cp_entry_username)) as total_shared_req,
            cp.*
      FROM
            car_pooling cp
      WHERE
            cp.cp_id = ".$cp_id;
      $res = DB::select($sql);      
      return response()->json(array('data'=>$res,'status'=>true));
   }

   function updateCapacity(Request $request){
     
  //dd($request->all());
      if($request->get('textNocap')=='Cancel My Request'){
            $delcap = "delete from car_pooling 
                     where  CP_PARTNER = '".$request->get('AuthUserNamee')."'
                     and cp_entry_username  = '".$request->get('user_ID')."' ";
         $res=DB::delete($delcap);
         $remove_partner = $request->get('AuthUserNamee');
         $updCapDell = "update car_pooling 
                           set  cp_partner = replace(CP_PARTNER,'$remove_partner','')  
                           where CP_WANT_RIDE not in  ('BE_SHARED')
                           and cp_entry_username  = '".$request->get('user_ID')."'";
                           //echo  $updCapDell ;
         //dd($request->all());
         $res=DB::insert($updCapDell);
         $ChekParentCpID = "select * from car_pooling where cp_id = '".$request->get('parent_cpID')."'";
         $resChekParentCpID = DB::select($ChekParentCpID);        
         $cap   = $resChekParentCpID[0]->cp_capacity+1;
         $updCap = "update car_pooling set cp_capacity = $cap where cp_id = '".$request->get('parent_cpID')."'";
         $res=DB::insert($updCap);
      }else if($request->get('textNocap')=='Cancel by Owner'){
     
         $delcap = "delete from car_pooling where  cp_id  = '".$request->get('cp_id')."' ";
         $res=DB::delete($delcap);
         $remove_partner = $request->get('user_ID');
         $updCapDell = "update car_pooling 
         set  cp_partner = replace(CP_PARTNER,'$remove_partner','')  
         where CP_WANT_RIDE not in  ('BE_SHARED')
         and cp_entry_username  = '".$request->get('AuthUserNamee')."'
         ";
         $res=DB::insert($updCapDell);
         $cap   = $request->get('cap')+1;
         $updCap = "update car_pooling set cp_capacity = $cap where cp_id = '".$request->get('cp_id')."'";
         $res=DB::insert($updCap);

         // Send Mail
         $to = strtolower($request->get('cpPartner')).'@bnu.edu.pk';
         $subject = 'Car Pooling Request Cancel';
         $message =  $request->get('cpPartner').' Your Car Pooling request has been cancelled by Owner '.$request->get('user_ID');
         $this->send_mail($to,$subject,$message);
     // exit();
   }else if($request->get('textNocap')=='Remove by Owner'){
           
      //dd($request->all());
         // $sqlParent = "select * from car_pooling where cp_id in 
         // (select parent_cp_id from car_pooling cpt where cpt.cp_id = ".$request->get('cp_id').") ";
         // $resParent = DB::select($sqlParent);

         $sqlParent = "select * from car_pooling where cp_id = '".$request->get('parent_cpID')."' ";
         $resParent = DB::select($sqlParent);
         
         $cap   = $resParent[0]->cp_capacity+1;

         $updCap = "update car_pooling set cp_capacity = $cap where cp_id = '".$request->get('parent_cpID')."'";
         //echo $updCap;
         $res=DB::insert($updCap);


         //dd($request->all());
         $sqlParentGetP = "select * from car_pooling where cp_id  = ".$request->get('cp_id')." ";
         $resParentGetP = DB::select($sqlParentGetP);
         
         $remove_partner   = $resParentGetP[0]->cp_partner;
         // echo $cap ;exit();
         
         //$remove_partner = $request->get('user_ID');
         $updCapDell = "update car_pooling 
         set  cp_partner = replace(CP_PARTNER,'$remove_partner','')  
         where CP_WANT_RIDE not in  ('BE_SHARED')
         and cp_entry_username  = '".$request->get('AuthUserNamee')."'
         ";
         // echo  $updCapDell;
         // dd($request->all());
         //echo  $updCapDell;exit();
         $res=DB::insert($updCapDell);
         $delcap = "delete from car_pooling where  cp_id  = '".$request->get('cp_id')."' ";
         $res=DB::delete($delcap);

         // Send Mail
         $to = strtolower($request->get('cpPartner')).'@bnu.edu.pk';
         $subject = 'Rollback Car Pooling Request by Owner';
         $message =  $request->get('cpPartner').' Your Car Pooling request has been rollback by Owner '.$request->get('user_ID');
         $this->send_mail($to,$subject,$message);
     
     
    }else{
      
         $cp_id   = $request->get('cp_id');
         $user_id   = $request->get('user_ID');
        
         $sql = "select * from car_pooling where cp_id = $cp_id";
         $res = DB::select($sql);

         // echo $sql;exit();
         // dd($res[0]->cp_partner);
         $cpConcate = $res[0]->cp_partner.'~'.$user_id;     
         $sqlParent = "select * from car_pooling where cp_id = ".$request->get('parent_cpID')." ";
         $resParent = DB::select($sqlParent);
         
         $cap   = $resParent[0]->cp_capacity-1;
         // echo $cap ;exit();
         $updCapone = "update 
                     car_pooling set cp_capacity = $cap , 
                     CP_PARTNER = '$cpConcate'
                     where cp_id =  ".$request->get('parent_cpID')." and  CP_WANT_RIDE  in  ('Offer Ride')";
                     // echo   $updCapone;exit();
         $res=DB::insert($updCapone);

         $updCap = "update car_pooling set
                     CP_WANT_RIDE='BE_SHARED'
                     where cp_id = $cp_id and  CP_WANT_RIDE  in  ('PENDING')";
         $res=DB::insert($updCap);

         $delRqeCarPooling = "delete from car_pooling where cp_partner = '".$request->get('cpPartner')."' and cp_entry_username not in  ('".$request->get('AuthUserNamee')."') and cp_want_ride = 'PENDING'";
         $resDelRqeCarPooling=DB::delete($delRqeCarPooling);


         // Send Mail 
         $to = strtolower($request->get('cpPartner')).'@bnu.edu.pk';
         $subject = 'Car Pooling Request Accepted';
         $message =  $request->get('cpPartner').' Your Car Pooling request has been Accepted by Owner '.$request->get('user_ID').'. Your Remaining requests has been cancelled automatically';
         $this->send_mail($to,$subject,$message);
         
      }
      return response()->json(array('data'=>$res,'status'=>true));
   } 

   function send_mail($to,$subject,$message){

      DB::table('testing')
      ->insert(
            ['description' => 'Car Pooling Mail From Faculty Side || To: '.$to.' || Subject: '.$subject.' || Message: '.$message]
      );
      // $procedurenotification = 'send_mail_one_by_one';
      // $bindings_notification = [
      // 'to'           => $to,
      // 'subject'      => $subject ,
      // 'message'      => $message,
      // ];      
      // return $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification); 
   }

   function car_pulling_timings(Request $request){
      $sql="SELECT
               cp_monday      AS monday,
               cp_tuesday     AS tuesday,
               cp_wednesday   AS wednesday,
               cp_thursday    AS thursday,
               cp_friday      AS friday,
               cp_saturday    AS saturday,
               cp_sunday      AS sunday,
               cp_specific_day as today
            FROM
               car_pooling
            WHERE
               cp_id = '".$request->input('cp_id')."'";
      $res = DB::select($sql);
      return response()->json(array('data'=>$res,'status'=>true));      
   }
   
   function addFeedback(Request $request){
    
      $rulesArr['description']    = 'required|min:3|max:4000';
        
       $validator = Validator::make($request->all(), $rulesArr,['description.required' => 'Description is required']);
       
       if ($validator->fails()) {
         return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()))->setStatusCode(400,$validator->errors()->first());
       }

     DB::table('student_feedbacks')->insert([
      'sfd_desc' =>$request->get('description'),
      'sai_enrollment_no' => auth()->user()->username,
      'sfd_title' => 'feedback'
       ]);

       return response()->json(array('status' => true,'data'=> [], 'message' => 'Data Insert Successfully'));

       
      }

      function pooling_feedback(Request $request){

         $data = DB::table('student_feedbacks')->where('sfd_title','feedback')->where('sai_enrollment_no',auth()->user()->username) ->orderBy('id', 'desc')->get();

         return view('CarPulling.feedback',['res'=> $data]);
      }

      function carRegistration(){
      
         $sqlAcademicSessions = "select sess_name from academic_sessions where sess_id in (select max(sc_co_sess_id) from student_courses)";
         $recsqlAcademicSessions = DB::select($sqlAcademicSessions); 
   
         $sql = "select * from vehicle_registration order by vr_id desc";
         $res = DB::select($sql);
       
         return view('CarPulling/car_registration',['academic_session'=>$recsqlAcademicSessions,'car_registration'=>$res,'page_action'=>'issue_token']);
       
      } 
      function searchRegistration(){
         
         $sqlAcademicSessions = "select sess_name from academic_sessions where sess_id in (select max(sc_co_sess_id) from student_courses)";
         $recsqlAcademicSessions = DB::select($sqlAcademicSessions); 
   
         $sql = "select * from vehicle_registration order by vr_id desc";
         $res = DB::select($sql);
       
         return view('CarPulling/car_registration',['academic_session'=>$recsqlAcademicSessions,'car_registration'=>$res,'page_action'=>'search_only']);
       
      } 
   
     function insertRecord(Request $request){       
         $rules=array(
            'sch_enrollment_no'  => "required",    
            // 'vehicle_no'           => "required",    
            'vehicle_token'     => "required",   
         );
         $validator=Validator::make($request->all(),$rules);
         if($validator->fails())
         {
            return $validator->errors();
         }
         $session          = $request->get('academic_session');
         $enrollment       = $request->get('sch_enrollment_no');
         $std_roll_no      = trim(explode("||",strtolower($enrollment))[0]);
         $vehicle_no       = $request->get('vehicle_no');
         $vehicle_token    = $request->get('vehicle_token');
         $parking_status    = $request->get('parking_status');
        
         //echo "123";exit;
         $insert_carRegistrion = DB::table('vehicle_registration')->insert([           
            'VR_SESS_ID'         => $session,
            'VR_USERNAME'        => $std_roll_no,
            'VR_VEHICLE_NO'      => $vehicle_no,//($vehicle_no!="") ? $vehicle_no :"CARNO",//'VR_VEHICLE_NO'      => '$vehicle_no',
            'VR_VEHICLE_TOKEN'   => $vehicle_token,
            'VR_PARKING'         => $parking_status,
            'VR_STATUS'          => 'E',
            'VR_ENTRY_USERID'    => auth()->user()->username,
         ]);
         if($insert_carRegistrion){         
            return response()->json(['status' => true, 'message' => '1']);
         }else{        
            return response()->json(['status' => false, 'message' => '0']);
         }
   
      }
   
      function getCarRegistration(){
   
         $sql = "select * from vehicle_registration ";
         $res = DB::select($sql);
         return response()->json(array('data'=>$res,'status'=>true));
      }
   
      function delRecord(Request $request){
         $vr_id = $request->get('vr_id');
         $reason = $request->get('reason');
         $updateCarStatus = "update VEHICLE_REGISTRATION set vr_status = 'D',vr_cancel_reason='$reason' where vr_id = $vr_id";
         $resupdateCarStatus = DB::insert($updateCarStatus);
          if($resupdateCarStatus){         
            return response()->json(['status' => true, 'message' => '1']);
         }
   
      }
      function updParkingStatus(Request $request){
         $vr_id = $request->get('vr_id');
         $parking_status = $request->get('parking_status');
         $updateCarStatus = "update VEHICLE_REGISTRATION set vr_parking = '$parking_status' where vr_id = $vr_id";
         $resupdateCarStatus = DB::insert($updateCarStatus);
         if($resupdateCarStatus){         
            return response()->json(['status' => true, 'message' => '1']);
         }
      }
      function directRegistration(){
         $sql="select
         (SELECT  max(vr_id) + 1
         FROM vehicle_registration
 
         ) as student_count,
         (
         SELECT  max(vr_id) + 1
         FROM vehicle_registration
  
         ) as faculty_count
         from dual";
         // $sql="select 
         // (SELECT count(*)+40
         // FROM vehicle_registration
         // WHERE REGEXP_COUNT(vr_username, '[0-9]') >= 3
         // ) as student_count,
         // (
         // SELECT count(*)+20
         // FROM vehicle_registration
         // WHERE (vr_username NOT LIKE '%2%' ESCAPE '\') AND (LOWER(vr_username) NOT LIKE '%sum%')
         // ) as faculty_count
         // from dual";
         $rec= DB::select($sql);
         return view('CarPulling/car_registration_direct',['rec'=>$rec]);
      }
      function insertDirectRecord(Request $request){
         $rules=array(
            'vr_username'        => "required",    
            'vr_vehicle_no'      => "required",    
            'vr_mobile'          => "required",   
            'vr_sticker'         => "required",    
            'vr_parking_status'  => "required"
         );
         $validator=Validator::make($request->all(),$rules);
         if($validator->fails())
         {
            return $validator->errors();
         }
         $vr_username          = $request->get('vr_username');
         $vr_vehicle_no        = $request->get('vr_vehicle_no');         
         $vr_mobile            = $request->get('vr_mobile');
         $vr_sticker           = $request->get('vr_sticker');
         $vr_parking_status    = $request->get('vr_parking_status');

        
         //echo "123";exit;
         $insert_carDirectRegistrion = DB::table('vehicle_registration')->insert([             
            'VR_USERNAME'        => $vr_username,
            'VR_VEHICLE_NO'      => $vr_vehicle_no,
            'VR_VEHICLE_TOKEN'   => $vr_sticker,
            'VR_PARKING'         => $vr_parking_status,
            'VR_MOBILE'          => $vr_mobile,
            'VR_STATUS'          => 'E',
            'VR_TYPE'            => 'Direct',
            'VR_ENTRY_USERID'    => auth()->user()->username,
         ]);
         if($insert_carDirectRegistrion){         
            return response()->json(['status' => true, 'message' => '1']);
         }else{        
            return response()->json(['status' => false, 'message' => '0']);
         }
      }
      function getDirectCarRegistration(){
         $sql= "select * from vehicle_registration where vr_type = 'Direct' order by vr_id desc";
         $res = DB::select($sql);
         return response()->json(array('data'=>$res,'status'=>true));
      }
      function editDirectCarRegistration(Request $request){
         $vr_id = $request->get('vr_id');
         $sql= "select * from vehicle_registration where vr_type = 'Direct' and vr_id =  $vr_id";
         $res = DB::select($sql);
         return response()->json(array('data'=>$res,'status'=>true));
      }
      function updateDirectRecord(Request $request){
         $vr_id = $request->get('vr_id');
         $rules=array(
            'vr_username'        => "required",    
            'vr_vehicle_no'      => "required",    
            'vr_mobile'          => "required",   
           // 'vr_sticker'         => "required",    
            'vr_parking_status'  => "required"
         );
         $validator=Validator::make($request->all(),$rules);
         if($validator->fails())
         {
            return $validator->errors();
         }
         $vr_username          = $request->get('vr_username');
         $vr_vehicle_no        = $request->get('vr_vehicle_no');         
         $vr_mobile            = $request->get('vr_mobile');
       //  $vr_sticker           = $request->get('vr_sticker');
         $vr_parking_status    = $request->get('vr_parking_status');
         $time = Carbon::now();

         $update_carDirectRegistrion = DB::table('vehicle_registration')
         ->where('vr_id', $vr_id)      
         ->update([
            'VR_USERNAME'        => $vr_username,
            'VR_VEHICLE_NO'      => $vr_vehicle_no,
           // 'VR_VEHICLE_TOKEN'   => $vr_sticker,
            'VR_PARKING'         => $vr_parking_status,
            'VR_MOBILE'          => $vr_mobile,          
            'VR_ENTRY_USERID'    => auth()->user()->username,
            'VR_ENTRY_DATETIME'  =>$time,
         ]);
         if($update_carDirectRegistrion){         
            return response()->json(['status' => true, 'message' => '1']);
         }else{        
            return response()->json(['status' => false, 'message' => '0']);
         }
      }
       function carPoolingPdf(){
         $data = DB::table('car_pooling')
         ->select('cp_want_ride', 'cp_contact_me') // Select the columns you want to fetch
         // ->where('column1', '=', 'some_value') // Add any conditions as needed
         ->get(); // Fetch the data
         $sqlStatistic="SELECT
                                 *
                           FROM
                                 (
                                    SELECT
                                       CASE
                                             WHEN cp_want_ride = 'Get Ride'     THEN
                                                'Ride Requested'
                                             WHEN cp_want_ride = 'Offer Ride'   THEN
                                                'Ride Offered'
                                             WHEN cp_want_ride = 'PENDING'      THEN
                                                'Pending Requests'
                                             ELSE
                                                'Accepted Requests'
                                       END AS cp_want_ride,
                                       tr as total,
                                       to_number(
                                             CASE
                                                WHEN cp_want_ride = 'Get Ride'     THEN
                                                   1
                                                WHEN cp_want_ride = 'Offer Ride'   THEN
                                                   2
                                                WHEN cp_want_ride = 'PENDING'      THEN
                                                   3
                                                ELSE
                                                   4
                                             END
                                       ) AS ORDER_ID
                                    FROM
                                       (
                                             SELECT
                                                COUNT(*) AS tr,
                                                cp_want_ride
                                             FROM
                                                car_pooling
                                             GROUP BY
                                                cp_want_ride
                                       )
                                 )
                           ORDER BY
                                 to_number(TRIM(ORDER_ID)) ASC
                        ";
               $sqlParking="select
                                    vr_parking
                                    ,count(*) as total_parking
                                    from vehicle_registration
                                    where vr_parking not in ('ASDF')
                              group by vr_parking
               ";
         $statisticParking=DB::select($sqlParking);
         $statisticData=DB::select($sqlStatistic);
         $html = view('CarPulling/Reports/carPoolingPdf', ['data'=>$data,'statisticData'=>$statisticData,'statisticParking'=>$statisticParking])->render(); // Render the Blade template and get the HTML content
         $dompdf = new Dompdf();
         $dompdf->loadHtml($html); // Load the HTML content
         $dompdf->render(); // Render the PDF
         $output = $dompdf->output(); // Get the PDF output as a string
         // Set the response headers to display the PDF in the browser
         $current_time = Carbon::now();
         $current_date = Carbon::now()->format('Y-m-d');
         $current_time_string = $current_time->toDateTimeString();
         return view('CarPulling/Reports/carPoolingPdf', ['data'=>$data,'statisticData'=>$statisticData,'statisticParking'=>$statisticParking]);

      }

}
