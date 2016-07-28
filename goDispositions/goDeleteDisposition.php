<?php
    #######################################################
    #### Name: goDeleteDisposition.php	               ####
    #### Description: API to delete specific statuses  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
		$campaign_id = $_REQUEST["campaign_id"];
		$campaign_id = mysqli_real_escape_string($link, $campaign_id);
		
		$statuses = $_REQUEST["statuses"];
	        $ip_address = mysqli_real_escape_string($_REQUEST['hostname']);
    
    ### Check Campaign ID if its null or empty
	if($campaign_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }
				
   		$queryOne = "SELECT campaign_id, status FROM vicidial_campaign_statuses $ul where campaign_id='$campaign_id';";
   		$rsltvOne = mysqli_query($link, $queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
			
			if($statuses != NULL){
				$deleteQueryB = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' AND status = '$statuses' LIMIT 1;"; 
   				$deleteResultB = mysqli_query($link, $deleteQueryB);			
			}else{
				$deleteQuery = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				//echo $deleteQuery;
			}
			
        ### Admin logs
				$SQLdate = date("Y-m-d H:i:s");
				$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Status $statuses from Campaign $campaign_id','DELETE FROM vicidial_campaign_statuses  WHERE status IN ($statuses) AND campaign_id=$campaign_id;');";
				$rsltvLog = mysqli_query($linkgo, $queryLog);

			
				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Campaign statuses doesn't exist.");
		}
	}//end

?>
