<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\ORFPNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
class MainController extends Controller
{
    public function mainlogic(){

        $Arfpnum = array();
        $Astatus = array();

        while(true){

            $Arfpnum2 = array();
            $Astatus2 = array();
            
            $url = "https://api.jodoo.com/api/v4/app/63ab830a62223e000761ca73/entry/63aab91362223e000761b8fc/data?limit=100";

            $single = "https://api.jodoo.com/api/v4/app/63ab830a62223e000761ca73/entry/63aab91362223e000761b8fc/data?limit=1&data_id=63b7e52a87dbd80007b39e04";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
            "Authorization: Bearer f2USblc7dxeusAF00WB6P101KzODjgM3",
            "Content-Type: text/plain",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $data = "@";

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            $result = json_decode($resp, true);
            curl_close($curl);
            
            $counter = count($result["data"])-1;
            for($x2=0; $x2<=$counter; $x2++){
                $Arfpnum2[] = $result["data"][$x2]["rfpnum"];
                $Astatus2[] = $result["data"][$x2]["status"];
            }

            

            $qcount = DB::table('Jodoo_Details')->count();
            try{
                
                echo "updated";
            }catch(Exception $e){
                echo "error";
            }

            /* $stats = DB::table('Jodoo_Details')
                    ->where('Status', 'In-Progress')
                    ->get(); */

            for($z=$counter; $z>=$qcount-1; $z--){
                try{
                    $rfpnum = $result["data"][$z]["rfpnum"];
                    $id = $result["data"][$z]["_id"];
                    $email = $result["data"][$z]["email"];
                    $status = $result["data"][$z]["status"];
                    $update = $result["data"][$z]["updateTime"];
                    $name = $result["data"][$z]["creator"]["name"];
                    DB::beginTransaction();
                    DB::table('Jodoo_Details')->insert([
                        'RF_number' => $rfpnum, 
                        'Data_id' => $id,
                        'Email' => $email,
                        'Status' => $status,
                        'AuditDate' => $update,
                        'AuditUser' =>$name]);
                    DB::commit();

                    Session::put('Prfpnum', $rfpnum);
                    Session::put('Pstatus', $status);
                    Mail::to($email)->send(new ORFPNotification());
                    Session::flush();

                    echo "Successfully inserted";
                }catch(Exception $e){
                    echo "Error";
                    DB::rollback();
                }
            }
            if($Arfpnum>=$Arfpnum2){
                try{
                    $limit = $qcount-1;
                    for($x3=0; $x3<=$limit-1; $x3++){
                        if($Astatus[$x3]!=$Astatus2[$x3]){
                            $eme = $result["data"][$x3]["email"];
                            $Prfpnum = $result["data"][$x3]["rfpnum"];
                            $Pstatus = $result["data"][$x3]["status"];
                            DB::beginTransaction();
                            DB::table('Jodoo_Details')->where('RF_number', $Prfpnum)->update(['Status' => $Pstatus]);
                            DB::commit();
                            Session::put('Prfpnum', $Prfpnum);
                            Session::put('Pstatus', $Pstatus);
                            Mail::to($eme)->send(new ORFPNotification());
                            Session::flush();
                        }       
                    }   
                }catch(PDOException $e){
                    DB::rollback();
                    echo "email not sent";
                }
            }
            

            unset($Arfpnum);
            unset($Astatus);

            for($x=0; $x<=$counter; $x++){
                $Arfpnum[] = $Arfpnum2[$x];
                $Astatus[] = $Astatus2[$x];
            }

            ini_set('max_execution_time', 180);
            sleep(3);
        }
    }
}
