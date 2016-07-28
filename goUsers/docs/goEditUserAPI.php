<?php

 ####################################################
 #### Name: goEditUserAPI.php                    ####
 #### Description: API to edit specific user     ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################
	

	 $url = "https://jameshv.goautodial.com/goAPI/goUsers/goAPI.php"; # URL to GoAutoDial API file
	 $postfields["goUser"] = ""; #Username goes here. (required)
	 $postfields["goPass"] = ""; #Password goes here. (required)
	 $postfields["goAction"] = ""; #action performed by the [[API:Functions]]
	 $postfields["responsetype"] = "json"; #json (required)
         $postfields["user"] = ""; #Desired value for user (required)
         $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 $output = json_decode($data);
	

	if ($output->result=="success") {
	   # Result was OK!
		echo "Update Success";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
