
<html>
<form method="POST" enctype="multipart/form-data" id="showLoading">
<input type="hidden" name="stage" value="upload">
<!--<input type="hidden" name="sample_prompt" id=sample_prompt value="">-->

<table align=center width="100%" border="0" cellpadding="5" cellspacing="0" >
<tr><td align="center" colspan="2">We STRONGLY recommend uploading only 16bit Mono 8k PCM WAV audio files(.wav)</td></tr>
  <tr>
        <td colspan="2" align="center"><B>Voice File to Upload:</B>
        <input type="file" name="audiofile" id="audiofile" value="" accept="audio/*"><input type="submit" name="uploadFile" style="cursor:pointer;" value="upload"></td>
  </tr>
  <tr>
        <td align="center" colspan="2"></td>
  </tr>
  <tr><td align=center colspan="2"> <?=$uploadfail?> </td><td align=right></td></tr>


</table>
</form>
</html>





<?php

 ####################################################
 #### Name: goAddListAPI.php                     ####
 #### Description: API to add new List		 ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Smatra	 ####
 #### License: AGPLv2                            ####
 ####################################################
	

        $url = "https://gadcs.goautodial.com/goAPI/goVoiceFiles/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "goAddVoiceFiles"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["audiofile"] = ""; #voice file. (required)

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
	
//	print_r($data);

	if ($output->result=="success") {
	   # Result was OK!
		echo "Uploaded a WAV file: ";	
	 } else {
	   # An error occured
	   	echo $output->result;
	}

?>
