function NameSet() {
	document.getElementById("Nametag").style.visibility = 'hidden';
	document.getElementById("Input").value = document.getElementById('noscript').innerHTML;
	document.getElementById("Chatbar").method = "get";
}

function Commands() {
	document.getElementById("Command-Panel").style.display = "block";
	document.getElementById("Messageboard").style.display = "none";
	document.getElementById("Commands").setAttribute("onclick","CommandsReset();return false;");
}

function CommandsReset() {
	document.getElementById("Command-Panel").style.display = "none";
	document.getElementById("Messageboard").style.display = "block";
	document.getElementById("Commands").setAttribute("onclick","Commands();return false;");
}

window.onload = function() {
	var Chat = document.getElementById("Messageboard");
	Chat.scrollTop = Chat.scrollHeight;
}
