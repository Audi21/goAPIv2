<?php
 /**
 * @file 		goGetLeads.php
 * @brief 		API for Getting Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Warren Ipac Briones <warren@goautodial.com>
 * @author     	Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    function remove_empty($array) {
    	return array_filter($array, '_remove_empty_internal');
    }

    function _remove_empty_internal($value) {
		return !empty($value) || $value === 0;
    }
    
    $goVarLimit = $astDB->escape($_REQUEST["goVarLimit"]);
	$userid = $astDB->escape($_REQUEST["user"]);
	$search = $astDB->escape($_REQUEST['search']);
	$disposition_filter = $astDB->escape($_REQUEST['disposition_filter']);
	$list_filter = $astDB->escape($_REQUEST['list_filter']);
	$address_filter = $astDB->escape($_REQUEST['address_filter']);
	$city_filter = $astDB->escape($_REQUEST['city_filter']);
	$state_filter = $astDB->escape($_REQUEST['state_filter']);
	$search_customers = $astDB->escape($_REQUEST['search_customers']);
	
	$goSearch = "";
	
	if($goVarLimit > 0) {
		$goMyLimit = "LIMIT $goVarLimit";
	} else {
		$goMyLimit = "LIMIT 10000";
	}
	
	if(!empty($search)) 
		$goSearch = "AND (phone_number LIKE '$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR lead_id LIKE '$search')";
	else
		$goSearch = '';
	if(!empty($disposition_filter))
		$filterDispo = "AND status = '$disposition_filter'";
	else
		$filterDispo = '';
	if(!empty($list_filter))
		$filterList = "AND list_id = '$list_filter'";
	else
		$filterList = '';
	if(!empty($address_filter))
		$filterAddress = "AND (address1 LIKE '%$address_filter%' OR address2 LIKE '%$address_filter%')";
	else
		$filterAddress = '';
	if(!empty($city_filter))
		$filterCity = "AND city LIKE '%$city_filter%'";
	else
		$filterCity = '';
	if(!empty($state_filter))
		$filterState = "AND state LIKE '%$state_filter%'";
	else
		$filterState = '';
//echo $userid;
	//$apiresults = array("result" => "success", "userid"=>$userid);
   		
 //  	$query = "SELECT list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,date_of_birth,entry_date,user,gender,comments FROM vicidial_list $goMyLimit";
   //	$rsltv = mysqli_query($link, $query);
   
	if($userid == null){
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}else{
		$getAllowedCampaigns_query = "SELECT vicidial_users.user_group, vicidial_user_groups.allowed_campaigns FROM vicidial_users, vicidial_user_groups WHERE vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user ='$userid'";  	
		$allowedCampaigns_result = mysqli_query($link, $getAllowedCampaigns_query);
		$allowedCampaignsFetch = mysqli_fetch_array($allowedCampaigns_result, MYSQLI_ASSOC);
		$allowedCampaigns = $allowedCampaignsFetch['allowed_campaigns'];
		
		// GET CUSTOMER LIST
			$customers_query = "SELECT lead_id FROM go_customers;";
			$exec_customers_query = mysqli_query($linkgo, $customers_query);
			$customers = array();
			while($fetch_customers = mysqli_fetch_array($exec_customers_query)){
				$customers[] = $fetch_customers['lead_id'];
			}
			
		//if admin
		if(preg_match("/ALL-CAMPAIGNS/", $allowedCampaigns)){
			$queryx = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM vicidial_list WHERE phone_number != '' $goSearch $filterDispo $filterList $filterAddress $filterCity $filterState $customersOnly $goMyLimit";
			$returnRes = $astDB->rawQuery($queryx);
		} else { //if multiple allowed campaigns
			$multiple_campaigns = explode("-", $allowedCampaigns);
			$allowedCampaignsx = $multiple_campaigns[0];
			$allowedCampaignsx = explode(" ",$allowedCampaignsx);	
			$allowedCampaignsx = remove_empty($allowedCampaignsx);
			$allowedCampaignsx = implode("','", $allowedCampaignsx);
			$allowedCampaignsx = "'".$allowedCampaignsx."'";
			
			//get lists id from return campaigns
			$getListsID_query = "select list_id from vicidial_lists where campaign_id IN($allowedCampaignsx)";
			$listsID_result = $astDB->rawQuery($getListsID_query);
			//$listsIDFetch = mysqli_fetch_array($listsID_result, MYSQLI_ASSOC);
			
			foreach ($listsID_result as $listsID_resultx){
				$list_results .= $listsID_resultx['list_id']." ";
			}
			$fetchLists = explode(" ", $list_results);
			$fetchLists = remove_empty($fetchLists);
			$fetchLists = implode("','", $fetchLists);
			//$fetchLists = "'".$fetchLists."'";
			
			if($fetchLists != '')
				$additional_query = "AND list_id IN('".$fetchLists."')";
			else
				$additional_query = '';
			//get all leads from return list_id
	//		$queryx = "SELECT count(*) as xxx FROM vicidial_list WHERE list_id IN($fetchLists);";
			$queryx = "SELECT lead_id,list_id,first_name,middle_initial,last_name,phone_number,status,last_local_call_time FROM vicidial_list WHERE phone_number != '' $additional_query $goSearch $filterDispo $filterList $filterAddress $filterCity $filterState $customersOnly $goMyLimit;";
			$returnRes = $astDB->rawQuery($queryx);
		}
		
		$data = array();
		foreach ($returnRes as $fresults){
			if(in_array($fresults['lead_id'], $customers)){
				$dataLeadid[] = $fresults['lead_id'];
				$dataListid[] = $fresults['list_id'];
				$dataFirstName[] = $fresults['first_name'];
				$dataMiddleInitial[] = $fresults['middle_initial'];
				$dataLastName[] = $fresults['last_name'];
				$dataPhoneNumber[] = $fresults['phone_number'];
				$dataDispo[] = $fresults['status'];
				$dataLastCallTime[] = $fresults['last_local_call_time'];
			}else{
				$dataLeadid2[] = $fresults['lead_id'];
				$dataListid2[] = $fresults['list_id'];
				$dataFirstName2[] = $fresults['first_name'];
				$dataMiddleInitial2[] = $fresults['middle_initial'];
				$dataLastName2[] = $fresults['last_name'];
				$dataPhoneNumber2[] = $fresults['phone_number'];
				$dataDispo2[] = $fresults['status'];
				$dataLastCallTime2[] = $fresults['last_local_call_time'];
			}
			array_push($data, $fresults);
		}
		if ($search_customers) {
			$apiresults = array("result" => "success", "lead_id" => $dataLeadid, "list_id" => $dataListid, "first_name" => $dataFirstName, "middle_initial" => $dataMiddleInitial, "last_name" => $dataLastName, "phone_number" => $dataPhoneNumber, "status" => $dataDispo, "last_call_time" => $dataLastCallTime, "data" => $data);
		} else {
			$apiresults = array("result" => "success", "lead_id" => $dataLeadid2, "list_id" => $dataListid2, "first_name" => $dataFirstName2, "middle_initial" => $dataMiddleInitial2, "last_name" => $dataLastName2, "phone_number" => $dataPhoneNumber2, "status" => $dataDispo2, "last_call_time" => $dataLastCallTime2, "data" => $data);
		}
	}
?>
