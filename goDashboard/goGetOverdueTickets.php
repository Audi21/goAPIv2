<?php
 /**
 * @file 		goGetOverdueTickets.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho  <demian@goautodial.com>
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

    $userid = $_REQUEST['userid'];
    $groupId = go_get_groupid($goUser, $astDB);
    
    if($userid == null && $userid == 0) { 
            $apiresults = array("result" => "Error: Set a value for User ID"); 
    } else {    
    
        if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
        } else { 
            $stringv = go_getall_allowed_users($groupId, $astDB);
            $stringv .= "'j'";
            $ul = "and vcl.campaign_id IN ($stringv) and user_level != 4";
        }
        
        $state = "open";
        $query = "SELECT count(*) as overduetickets FROM ost_ticket WHERE status_id IN (SELECT id AS status_id FROM ost_ticket_status WHERE state='$state') AND dept_id IN ((select dept_id from ost_staff where staff_id='$userid'),(SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid')) AND isoverdue=1";

        $fresults = $ostDB->rawQuery($query);
        //$fresults = mysqli_fetch_assoc($rsltv);
        $apiresults = array_merge( array( "result" => "success" ), $fresults);
        
    }
?>
