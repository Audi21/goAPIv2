<?php
/**
 * @file        goGetListInfo.php
 * @brief       API to get specific list details
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
    //////////////////////////////////////////////////////
    /// Name: goGetListInfo.php 		///
    /// Description: API to get specific List 		///
    /// Version: 0.9 		///
    /// Copyright: GOAutoDial Inc. (c) 2011-2014 		///
    /// Written by: Jermiah Sebastian Samatra 		///
    /// License: AGPLv2 		///
    //////////////////////////////////////////////////////
    
    // POST or GET Variables
    $list_id = $astDB->escape($_REQUEST['list_id']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
    
	if($list_id == null) {
		$err_msg = error_handle("10107");
		$apiresults = array("code" => "10107", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for List ID."); 
	} else {
 
    	$groupId = go_get_groupid($goUser, $astDB);
    
		if (!checkIfTenant($groupId, $goDB)) {
        	$ul = "WHERE vicidial_lists.list_id='$list_id'";
    	} else { 
			$ul = "WHERE vicidial_lists.list_id='$list_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT
			vicidial_lists.list_id,vicidial_lists.list_name,vicidial_lists.list_description,
			(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,
			(SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count,
			vicidial_lists.active,vicidial_lists.list_lastcalldate,vicidial_lists.campaign_id,vicidial_lists.reset_time,vicidial_lists.web_form_address,
			vicidial_lists.agent_script_override,vicidial_lists.campaign_cid_override,vicidial_lists.drop_inbound_group_override,
			vicidial_list.called_since_last_reset as reset_called_lead_status,
			vicidial_lists.xferconf_a_number,vicidial_lists.xferconf_b_number,vicidial_lists.xferconf_c_number,vicidial_lists.xferconf_d_number,
			vicidial_lists.xferconf_e_number
		FROM vicidial_lists
		LEFT JOIN vicidial_list
		ON vicidial_lists.list_id=vicidial_list.list_id
		$ul order by list_id LIMIT 1";
   		$rsltv = $astDB->rawQuery($query);
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			foreach ($rsltv as $fresults) {
				$dataListId[] =  $fresults['list_id'];
				$dataListName[] =  $fresults['list_name'];
				$dataActive[] =  $fresults['active'];
				$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
				$dataTally[] =  $fresults['tally'];
				$dataCFCount[] =  $fresults['cf_count'];
				$dataCampaignId[] =  $fresults['campaign_id'];
				$datareset_called_lead_status[] =  $fresults['reset_called_lead_status'];
				$dataweb_form_address[] =  $fresults['web_form_address'];
				$dataagent_script_override[] =  $fresults['agent_script_override'];
				$datacampaign_cid_override[] =  $fresults['campaign_cid_override'];
				$datadrop_inbound_group_override[] =  $fresults['drop_inbound_group_override'];
				$datareset_time[] = $fresults['reset_time'];
				$datalist_desc[] = $fresults['list_description'];
				$dataxferconf_a_number[] = $fresults['xferconf_a_number'];
				$dataxferconf_b_number[] = $fresults['xferconf_b_number'];
				$dataxferconf_c_number[] = $fresults['xferconf_c_number'];
				$dataxferconf_d_number[] = $fresults['xferconf_d_number'];
				$dataxferconf_e_number[] = $fresults['xferconf_e_number'];
			}
			
			$log_id = log_action($goDB, 'VIEW', $log_user, $ip_address, "Viewed the info of List ID: $list_id", $log_group);
			
			$apiresults = array(
				"result" => "success",
				"list_id" => $dataListId,
				"list_name" => $dataListName,
				"active" => $dataActive,
				"list_lastcalldate" => $dataListLastcallDate,
				"tally" => $dataTally,
				"cf_count" => $dataCFCount,
				"campaign_id" => $dataCampaignId,
				"reset_called_lead_status" => $datareset_called_lead_status,
				"web_form_address" => $dataweb_form_address,
				"agent_script_override" => $dataagent_script_override,
				"campaign_cid_override" => $datacampaign_cid_override,
				"drop_inbound_group_override" => $datadrop_inbound_group_override,
				"reset_time" => $datareset_time,
				"list_description" => $datalist_desc,
				"xferconf_a_number" => $dataxferconf_a_number,
				"xferconf_b_number" => $dataxferconf_b_number,
				"xferconf_c_number" => $dataxferconf_c_number,
				"xferconf_d_number" => $dataxferconf_d_number,
				"xferconf_e_number" => $dataxferconf_e_number
			);
		} else {
			$err_msg = error_handle("41004", "list_id. Doesn't exist.");
			$apiresults = array("code" => "41004", "result" => $err_msg);
			//$apiresults = array("result" => "Error: List doesn't exist.");
		}
	}
?>
