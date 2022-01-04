$(document).foundation()

window.addEventListener('load',paginator);
window.addEventListener('hashchange',paginator);

function paginator(){
	hash = window.location.hash.substr(1);
	let dimmeable_list = document.getElementsByClassName("dimmeable");
	for(el of dimmeable_list){
		el.style.display = "none";
	}
	if (hash=="") {
		document.getElementById("navigator").style.display = "block";
	}
	else{
		let el = document.getElementById(hash);
		if(el){
			el.style.display = "block";
		}
		else{
			document.getElementById("navigator").style.display = "block";
		}
	}
}