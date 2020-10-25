<?php
global $xoopsConfig;
global $xoopsUser;


require 'includes/fileupload-class.php';
$path	= $xoopsConfig['root_path'].'/uploads/';
$upload_file_name	= 'banlog';
$acceptable_file_types	= 'text/plain';
$default_extension	= '.cfg';
$mode	= 1;

if (isset($_REQUEST['submitted'])) {

	function ban_file($filename) {
		global $path;
		$filename	= $path.$filename;
		$array		= ['VALVE_ID_LAN'];
		$dump			= file($filename);
		$var			= '';
		$count		= count($dump);

?>

	<table border="1" cellpadding="4" cellspacing="0" width="100%">
		<tr>
			<td colspan="3"><b>Processing bans...</b></b></td>
		</tr>

		<tr>
			<td width="1%">&nbsp;</td>
			<td width="32%"><i>SteamID/IP</i></td>
			<td width="67%"><i>Result</i></td>
		</tr>

<?php

		$j = 0;
		for ($i=0; $i<$count; $i++) {
			$var = trim($dump[$i]);
			if(!empty($var)){
				if(!eregi($array,$var)){
					$j++;
					$each = explode(' ', $var);

					if (substr($each[2], 0, 5) == 'STEAM') {
						$ban_type = 'S';
					} else {
						$ban_type = 'I';
					} 
					global $xoopsUser; 
					
					$result = AddImportBan($each[2], 'unknown (imported)', $xoopsUser->getVar('uname'), $_SERVER['REMOTE_ADDR'], $ban_type, $_REQUEST['ban_reason'], $_REQUEST['ban_length']);

?>

			<tr bgcolor="#f0ffff">
				<td><?=$j ?></td>
				<td><?=$each[2] ?></td>
				<td><?=$result ?></td>
			</tr>

<?php

				}
			}
			$var= '';
		}
		echo '<tr><td></td></tr>';
		echo '<tr><td colspan="3" align="right"><b><a href="index.php">ok</a></b></td></tr></table>';
	}

	$my_uploader = new uploader($_POST['en']);
	$my_uploader->max_filesize(45000);

	if ($my_uploader->upload($upload_file_name, $acceptable_file_types, $default_extension)) {
		$my_uploader->save_file($path, $mode);
	}
	
	if ($my_uploader->error) {
		echo $my_uploader->error . "<br><br>\n";	
	} else {
		ban_file($my_uploader->file['name']);
	}


	if ($CFG->use_logfile == 'yes') {
		$remote_address = $_SERVER['REMOTE_ADDR'];
		$user						= $_SESSION['uid'];

		AddLogEntry("$remote_address | $user | Imported a number of bans");
	}

	
} else {

?>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
	<tr>
		<td colspan="2"><b>Importing bans...</b></b></td>
	</tr>
	<form enctype="multipart/form-data" action="index.php?task=import_bans" method="POST">
	<input type="hidden" name="submitted" value="true">

    <?php

//if ($CFG->auth_method == "sessions") {

?>

	<input type="hidden" name="admin_nick" value="<?=$xoopsUser->getVar('uname');?>">

    <?php

//}

?>
		
		<tr>
			<td width="30%" bgcolor="#f0ffff">Banfile: </td>
			<td bgcolor="#f0ffff"><input name="<?= $upload_file_name; ?>" type="file" style="font: 8pt bold"></td>
		</tr>

		<tr>
			<td width="30%" bgcolor="#f0ffff">Reason: </td>
			<td bgcolor="#f0ffff"><input type="text" name="ban_reason" size="40" style="font: 8pt bold"></td>
		</tr>

		<tr>
			<td width="30%" bgcolor="#f0ffff">Ban Length: </td>
			<td bgcolor="#f0ffff"><input type="text" name="ban_length" size="40" style="font: 8pt bold"></td>
		</tr>

    <?php

if (isset($acceptable_file_types) && trim($acceptable_file_types)) {

?>

		<tr>
			<td colspan="2" align="right">This form only accepts <b><?php print str_replace('|', ' or ', $acceptable_file_types); ?></b> files &nbsp;&nbsp;<input type="submit" name="importit" value="Upload File" style="font: 8pt bold"></td>
		</tr>
		</form>
	</table>

    <?php
}

}
?>
