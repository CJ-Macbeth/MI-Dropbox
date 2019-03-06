<?php
	session_start();
	$Database = new mysqli('localhost', 'root', 'Curious', 'Dropbox');
	function Type($File) {
		$Music = array("aif", "cda", "mid", "midi", "mp3", "mpa", "ogg", "wav", "wma", "wpl");
		$Compressed = array("7z", "arj", "deb", "pkg", "rar", "rpm", "tar", "z", "zip");
		$DiskImage = array("bin", "dmg", "iso", "toast", "ved");
		$Database = array("csv", "dat", "db", "dbf", "log", "mdb", "sav", "sql", "tar", "xml");
		$Executable = array("apk", "bat", "bin", "cgi", "pl", "com", "exe", "gadget", "jar", "wsf");
		$Font = array("fnt", "fon", "otf", "ttf");
		$Image = array("ai", "bmp", "gif", "ico", "jpg", "jpeg", "png", "ps", "psd", "svg", "tif", "tifl");
		$Programming = array("asp", "aspx", "cer", "cfm", "rss", "xhtml", "c", "class", "cpp", "cs", "cs", "h", "swift", "vb");
		$Presentation = array("keg", "odp", "pps", "ppt", "pptx", "ods", "xlr", "xls", "xlsx");
		$Video = array("3g2", "3gp", "avi", "flv", "h264", "m4v", "mkv", "mov", "mp4", "mpg", "mpeg", "rm", "swf", "vob", "wmv");
		$Text = array("doc", "docx", "odt", "pdf", "rtf", "tex", "txt", "wks", "wps", "wpd");
		$HTML = array("html", "htm");
		$sFile = (explode(".",$File));
		if (in_array($sFile[1], $Music)) {return "Music";}
		elseif (in_array($sFile[1], $Compressed)) {return "Compressed";}
		elseif (in_array($sFile[1], $DiskImage)) {return "DiskImage";}
		elseif (in_array($sFile[1], $Database)) {return "Database";}
		elseif (in_array($sFile[1], $Executable)) {return "Executable";}
		elseif (in_array($sFile[1], $Font)) {return "Font";}
		elseif (in_array($sFile[1], $Image)) {return "Image";}
		elseif (in_array($sFile[1], $Programming)) {return "Programming";}
		elseif (in_array($sFile[1], $Presentation)) {return "Presentation";}
		elseif (in_array($sFile[1], $Video)) {return "Video";}
		elseif (in_array($sFile[1], $Text)) {return "Text";}
		elseif (in_array($sFile[1], $HTML)) {return "HTML";}
		elseif ($sFile[1] == "py") {return "Python";}
		elseif ($sFile[1] == "css") {return "CSS";}
		elseif ($sFile[1] == "java") {return "Java";}
		elseif ($sFile[1] == "js") {return "JavaScript";}
		elseif ($sFile[1] == "php") {return "PHP";}
		elseif ($sFile[1] == "sh") {return "SH";}
		else {return "Uncatagorized";}
	}
	function FileboxIteration($Directory, $Path = '') {
		$FullPath = $Path.$Directory."/";
		$Files = new DirectoryIterator($FullPath);
		$ID = md5($FullPath);
		echo '<details class="Filebox" open><summary class="Filebox-Header">',$Directory,'</summary>';
		foreach ($Files as $File) {
			$Type = Type($File);
			if ($File->getFilename()[0] != ".") { echo '<form class="File" action="ControlPanel.php" method="post"><input type="Text" name="FileRemove" value="',$File,'" style="display:none;"><a href="'.$FullPath.$File.'" download><img src="IconPack/',$Type,'.png" class="FileImage" /></a><a href="'.$FullPath.$File.'" target="_blank" class="FileInfo Highlight"><input class="Filebox-Upload-Button" type="image" src="IconPack/Remove.png" alt="-" /><p>'.$File.'</p></a></form>'; }
		}
		echo '</details>';
	}
	if (isset($_POST['FileRemove'])) {
		exec("rm Shared/'".$_POST['FileRemove']."'");
	}
	if (isset($_POST['Input'])) {
		if ($_POST['Input'][0] != '$') {
			if ($Database->query("insert into Messageboard (Name, Message) values ('".$_SESSION['Name']."', '".str_replace("'", '&#39', filter_var($_POST['Input'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW))."')")) {
				$Return = '<script>window.location="ControlPanel.php"</script>';
			} else {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Message upload failure</p>';
			}
		} elseif (substr($_POST['Input'], 0, 16) == '$Remove Message ') {
			if ($Database->query("delete from Messageboard where Number = '".substr($_POST['Input'], 16)."'")) {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Message removal success :D<p/>';
			} else {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Message removal failed :/<p/>';
			}
		} elseif ($_POST['Input'] == '$Signatures') {
			if ($Result = $Database->query("select * from Signatures")) {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Signatures:</p><br>';
				while ($Row = $Result->fetch_assoc()) {
					$Return .= '<p class="Message"><span class="Bot">'.$Row['Number'].'</span><b class="Highlight">'.$Row['Name'].'</b> signed:<br>"'.$Row['Message'].'"<br>on <span class="Bot">'.$Row['Date'].'</span><br><img src="Signatures/'.$Row['Image'].'" alt="(No image)" style="max-width:100%" /></p><br>';
				}
			}
		} elseif (substr($_POST['Input'], 0, 18) == '$Remove Signature ') {
			if ($Result = $Database->query("select Image from Signatures where number = '".substr($_POST['Input'], 18)."'")->fetch_assoc()) {
				if (!exec("rm Signatures/".$Result['Image']) && $Database->query("delete from Signatures where number = '".substr($_POST['Input'], 18)."'")) {
					$Return = '<p class="Message"><b class="Bot">Bot</b>: Signature removal success :D<p/>';
				}
			} else {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Signature removal failed :/<p/>';
			}
		} else {
			$Return = '<p class="Message"><b class="Bot">Bot</b>: I don\'t recognize that command<p/>';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Control Panel</title>
		<link rel="stylesheet" href="Theme.css" />
		<style>
			.Message {color: black;}
			.Navigation {background-color: black;}
			.Navigation:hover {color : fuchsia; background-color: black;}
			.Navigation:active {color: black; background-color: fuchsia;}
			#Messageboard {border-color: black;}
			#Time {color: black;}
			#Chatbar {background-color: black; border-color: black;}
			#Input {background-color: black;}
			#Command-Panel {border-color: black;}
			.Filebox-Header {background-color: black;}
			.Highlight {color: fuchsia; border-color: black;}
			#Filebox-Area {border-color: black;}
			#Messageboard {background-image: url(IconPack/Map.png);}
			#Command-Panel {background-image: url(IconPack/Map.png);}
			#Filebox-Area {background-image: url(IconPack/Map.png);}
			.Filebox {border-color: black;}
			.Bot {color: blue;}
		</style>
		<script src="Theme.js"></script>
		<noscript id="noscript"><?php echo $_SESSION['Name']; ?></noscript>
	</head>
	<body>
		<div id="Flag-Area">
			<img id="Flag" src="IconPack/ControlPanelFlag.png" />
		</div>
		<div id="Navigation-Area">
			<a href="Dropbox.php"><button class="Navigation">Dropbox</button></a>
			<a href="ControlPanel.php"><button class="Navigation">Update Page</button></a>
		</div>
		<div id="Message-Area">
			<div id="Messageboard">
				<?php
					if ($Result = $Database->query("select * from Messageboard")) {
						while ($Row = $Result->fetch_assoc()) {
							echo '<p class="Message"><b class="Highlight">'.$Row['Name'].'</b>: '.$Row['Message'].'<span class="Bot" style="float:right;">'.$Row['Number'].'</span></p>';
						}
						$Result->free();
					} else { echo '<p class="Message"><b class="Bot">Bot</b>: Message processing failure</p>'; }
					echo $Return;
					echo '<div id="Time" class="Message"><b>Last Updated: <span class="Bot">',date("h:i:sa"),'</span></b></div>';

				?>
			</div>
			<form id="Chatbar" action="ControlPanel.php" method="post">
				<span id="Nametag" class="Name" onclick="NameSet()"><?php echo $_SESSION['Name'][0] ?></span><span class="Name">:</span>
				<input id="Input" name="Input" type="text" maxlength="16777215" placeholder="" required />
				<input id="Send" type="image" src="IconPack/Send.png" alt="+" />
				<input id="Commands" type="image" src="IconPack/Commands.png" alt="$" onclick="Commands();return false;" />
			</form>
			<div id="Command-Panel">
				<br>
				<p class="Message"><b class="Highlight">$Remove Message [number]</b>: Remove messages by number</p>
				<p class="Message"><b class="Highlight">$Signatures</b>: Displays all of the signatures other users have left on Thoth</p>
				<p class="Message"><b class="Highlight">$Remove Signature [number]</b>: Remove signatures by number</p>
			</div>
		</div>
		<div id="Filebox-Area">
			<?php
				FileboxIteration("Shared");
			?>
		</div>
	</body>
</html>
