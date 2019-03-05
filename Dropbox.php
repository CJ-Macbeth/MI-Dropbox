<?php
	session_start();
	if (isset($_GET['Input'])) {
		$_SESSION['Name'] = preg_replace("/[^A-Za-z0-9]/", '', $_GET['Input']);
	} elseif (!isset($_SESSION['Name'])) {
		$_SESSION['Name'] = V;
	}
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
		echo '<details class="Filebox" open><summary class="Filebox-Header">',$Directory,'</summary><form class="Filebox-Upload-Bar" action="Dropbox.php" method="post" enctype="multipart/form-data"></input><input id="',$ID,'" class="Filebox-Upload-Select" name="File" type="file" /><label for="',$ID,'"><img class="Filebox-Upload-Symbol" src="IconPack/Dropbox.png" /></label><div class="Filebox-Upload-Text"><input class="Filebox-Upload-Button" type="image" src="IconPack/Upload.png" alt="+" /><p><b>Select a file to upload</b></p></div></form>';
		foreach ($Files as $File) {
				if ($File->getFilename()[0] != ".") {echo '<div class="File"><a href="'.$FullPath.$File.'" download><img src="IconPack/',Type($File),'.png" class="FileImage" /></a><a href="'.$FullPath.$File.'" target="_blank" class="FileInfo Highlight"><p>'.$File.'</p></a></div>'; }
		}
		echo '</details>';
	}
	function Upload() {
		if (file_exists("Shared/".$_FILES['File']['name'])) {
			if (move_uploaded_file($_FILES['File']['tmp_name'],"Shared/".time().$_FILES['File']['name'])) {
				exec("sudo python Alert.py");
			}
		} else {
			if (move_uploaded_file($_FILES['File']['tmp_name'],"Shared/".$_FILES['File']['name'])) {
				exec("sudo python Alert.py");
			}
		}
	}
	if (isset($_FILES['File'])) {
		Upload();
		$Return = '<script>window.location="Dropbox.php"</script>';
	}
	if (isset($_GET['S'])) {
		$Return = '<form action="Dropbox.php" method="post" enctype="multipart/form-data"><p class="Message Highlight">Name:<br></p><input type="text" name="Name" style="width:90%;border:2%;margin:3%;font-size:3vh;" required /><br><p class="Message Bot">Message:<br></p><textarea name="Message" style="width:90%;max-width:90%;border:2%;margin:3%;height:20vh;" placeholder="Leave a message, something about yourself, or anything really. Know any good books? Quotes? Movies? ..." required></textarea><br><p class="Message Bot">Click Left to Choose/Take a picture, Right to submit Signature:</p><br><p class="Message"><input type="file" id="Image" name="Image" accept="image/*;capture=camera" class="Filebox-Upload-Select" /><label for="Image"><img class="Filebox-Upload-Symbol" src="IconPack/Dropbox.png" style="width:45%"/></label><input type="image" src="IconPack/Upload.png" class="Filebox-Upload-Symbol" style="float:right;width:45%"/></p></form>';
	}
	if (isset($_POST['Name']) && isset($_POST['Message'])) {
		$N = time();
		if ($Database->query("insert into Signatures (Name, Date, Message, Image) values ('".str_replace("'", '&#39', filter_var($_POST['Name'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW))."', '".date("F j, Y, g:i a")."', '".str_replace("'", '&#39', filter_var($_POST['Message'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW))."', '".$N."')")) {
			if (isset($_FILES['Image'])) { move_uploaded_file($_FILES['Image']['tmp_name'],"Signatures/".$N); }
			$Return = '<p class="Message"><b class="Bot">Bot</b>: Signature addition successful! :D</p>';
		} else {
			$Return = '<p class="Message"><b class="Bot">Bot</b>: Signature addition failed :/</p>';
		}
	}
	if (isset($_POST['Input'])) {
		if ($_POST['Input'][0] != '$') {
			if ($Database->query("insert into Messageboard (Name, Message) values ('".$_SESSION['Name']."', '".str_replace("'", '&#39', filter_var($_POST['Input'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW))."')")) {
				$Return = '<script>window.location="Dropbox.php"</script>';
			} else {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Message upload failure</p>';
			}
		} elseif ($_POST['Input'] == '$Controls') {
			$Return = '<p class="Message"><b class="Bot">Bot</b>: Click <a href="ControlPanel.php" target="_blank" class="Highlight">here</a> to enter the Control Panel </p>';
		} elseif ($_POST['Input'] == '$Sign') {
			$Return = '<form action="Dropbox.php" method="post" enctype="multipart/form-data"><p class="Message Highlight">Name:<br></p><input type="text" name="Name" style="width:90%;border:2%;margin:3%;font-size:3vh;" required /><br><p class="Message Bot">Message:<br></p><textarea name="Message" style="width:90%;max-width:90%;border:2%;margin:3%;height:20vh;" placeholder="Leave a message, something about yourself, or anything really. Know any good books? Quotes? Movies? ..." required></textarea><br><p class="Message Bot">Click Left to Choose/Take a picture, Right to submit Signature:</p><br><p class="Message"><input type="file" id="Image" name="Image" accept="image/*;capture=camera" class="Filebox-Upload-Select" /><label for="Image"><img class="Filebox-Upload-Symbol" src="IconPack/Dropbox.png" style="width:45%"/></label><input type="image" src="IconPack/Upload.png" class="Filebox-Upload-Symbol" style="float:right;width:45%"/></p></form>';
		} elseif ($_POST['Input'] == '$Download MI: Dropbox') {
			$Return = '<p class="Message"><b class="Bot">Bot</b>: Click <a class="Highlight" href="MI: Dropbox.zip" download>here</a> to download MI: Dropbox build instructions</p>';
		} elseif ($_POST['Input'] == '$Logout') {
			$_SESSION['Name'] = 'V';
			$Return = '<script>window.location="Dropbox.php"</script>';
		} elseif ($_POST['Input'] == '$Signatures') {
			if ($Result = $Database->query("select * from Signatures")) {
				$Return = '<p class="Message"><b class="Bot">Bot</b>: Signatures:</p><br>';
				while ($Row = $Result->fetch_assoc()) {
					$Return .= '<p class="Message"><b class="Highlight">'.$Row['Name'].'</b> signed:<br>"'.$Row['Message'].'"<br>on <span class="Bot">'.$Row['Date'].'</span><br><img src="Signatures/'.$Row['Image'].'" alt="(No image)" style="max-width:100%" /></p><br>';
				}
			}
		} else {
			$Return = '<p class="Message"><b class="Bot">Bot</b>: I don\'t recognize that command :/</p>';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Dropbox</title>
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
			<img id="Flag" src="IconPack/Flag.png" />
		</div>
		<div id="Navigation-Area">
			<button class="Navigation" onclick="alert('Welcome to the MI: Dropbox! Share files, talk in the chat, or add a signature to the collection -Mist Ink.')">About</button>
			<a href="Dropbox.php?S=1"><button class="Navigation">Leave a Signature</button></a>
			<a href="Dropbox.php"><button class="Navigation">Update Page</button></a>
		</div>
		<div id="Message-Area">
			<div id="Messageboard">
				<?php
					if ($Result = $Database->query("select * from Messageboard")) {
						while ($Row = $Result->fetch_assoc()) {
							echo '<p class="Message"><b class="Highlight">'.$Row['Name'].'</b>: '.$Row['Message'].'</p>';
						}
						$Result->free();
					} else { echo '<p class="Message"><b class="Bot">Bot</b>: Message processing failure</p>'; }
					echo $Return;
					echo '<div id="Time" class="Message"><b>Last Updated: <span class="Bot">',date("h:i:sa"),'</span></b></div>';

				?>
			</div>
			<form id="Chatbar" action="Dropbox.php" method="post">
				<span id="Nametag" class="Name" onclick="NameSet()"><?php echo $_SESSION['Name'][0] ?></span><span class="Name">:</span>
				<input id="Input" name="Input" type="text" maxlength="16777215" placeholder="" required />
				<input id="Send" type="image" src="IconPack/Send.png" alt="+" />
				<input id="Commands" type="image" src="IconPack/Commands.png" alt="$" onclick="Commands();return false;" />
			</form>
			<div id="Command-Panel">
				<br>
				<p class="Message"><b class="Highlight">$Sign</b>: Leave a signature containing your name, a message to other users, and a picture if you choose</p>
				<p class="Message"><b class="Highlight">$Signatures</b>: Displays all of the signatures other users have left on Thoth</p>
				<p class="Message"><b class="Highlight">$Download MI: Dropbox</b>: Download the full set of materials and instructions for building MI: Dropbox</p>
				<p class="Message"><b class="Highlight">$Logout</b>: Reset your Messageboard name to V (as if you couldn't do that yourself...)</p>
			</div>
		</div>
		<div id="Filebox-Area">
			<?php
				FileboxIteration("Shared");
			?>
		</div>
	</body>
</html>
