<?php

$open_page = false;

//check if address holds access token
$url = "https://xchain.io/api/holders/".$asset_name;   
$holders = @file_get_contents($url);
$holders_array = json_decode($holders, true);

if($holders_array != null){
    for($i=0; $i < count($holders_array["data"]); $i++){
        if($holders_array["data"][$i]["address"] == $_GET["addr"]){
           
            //get message to sign
            $msg = $_GET["msg"];

            //Sign message via Freeport public versig server
            $url = "https://files-sxngdyyoox.now.sh/versig?addr=".$_GET["addr"]."&msg=".$msg."&sig=".urlencode($_GET["sig"]);   
            $versig = @file_get_contents($url);
            $versig_object = json_decode($versig);
            if($versig_object == null){
                $versig_array["verified"] = 0;
            } else {
                $versig_array = get_object_vars($versig_object);
            }

            //check if verified
            if($versig_array["verified"] == 1){

                $current_time = time();

                $lower_limit = $msg - 21600; //valid for 6 hours before time signed
                $upper_limit = $msg + 21600; //valid for 6 hours after time signed

                //check if timestamp is +/- 6 hrs from current time
                if($_GET["msg"] > $lower_limit && $_GET["msg"] < $upper_limit){
                    //set to open page
                    $open_page = true;         
                } 

            }  
            
            
            
            
        }
    }    
}                    
                    
//deny access if checks are not ok
if($open_page == false){   
    http_response_code(404);
    die();      
}


?>