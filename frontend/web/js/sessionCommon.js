if(window.sessionStorage){
	if(!sessionStorage.getItem('lastHref')){
		sessionStorage.setItem('lastHref',location.href);
	}else{
		var hostFlag="/"==location.pathname?true:false;
		var lastHref=sessionStorage.getItem('lastHref');
		//當前爲首頁
		if(hostFlag){
			var lastDetailFlag=lastHref.match(/party\/[\da-zA-Z]{24}/);
			if(lastDetailFlag){
				window.detail2index=true;
			}
		}
		sessionStorage.setItem('lastHref',location.href);
	}	
}