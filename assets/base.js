
function vote(el, id) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "/level.php?id="+id+"&vote", true);
	xhttp.send();

	el.innerHTML = 'Liked!';
	el.disabled = 'disabled';
}

function play(id) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "/level.php?id="+id+"&lal", true);
	xhttp.send();

	document.location = "apparatus://"+id;
}