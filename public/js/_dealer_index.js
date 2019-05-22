function copy(id){
	var copyText = document.getElementById(id);
  copyText.select();
  document.execCommand("copy");
  
}

