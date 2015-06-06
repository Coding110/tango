
function tango(){
	var obj = document.getElementById('sf_result'); //media-result
	if(obj==null) return;
	//console.dir(obj);
	var video_links = obj.getElementsByClassName('link-box')[0].getElementsByTagName('a');
	//console.log(video_links.length);
	var results = new Array();
	item = {resolution:'240p', format:'mp4', url:'http://***'};
	for(i=0;i<video_links.length;i++){
		
		if(video_links[i].innerHTML.search('240p') != -1/* && video_links[i].innerHTML.search('MP4') != -1*/){
			console.log(video_links[i].innerHTML + ", HREF = " + video_links[i].href);
			//item = new Array();
			item['resolution'] = '240p';
			item['format'] = 'mp4';
			item['url'] = video_links[i].href;
			results.push(cloneObject(item));
		}else if(video_links[i].innerHTML.search('360p') != -1/* && video_links[i].innerHTML.search('MP4') != -1*/){
			console.log(video_links[i].innerHTML + ", HREF = " + video_links[i].href);
			//item = new Array();
			item['resolution'] = '360p';
			item['format'] = 'mp4';
			item['url'] = video_links[i].href;
			results.push(cloneObject(item));
		}else if(video_links[i].innerHTML.search('480p') != -1/* && video_links[i].innerHTML.search('MP4') != -1*/){
			console.log(video_links[i].innerHTML + ", HREF = " + video_links[i].href);
			//item = new Array();
			item['resolution'] = '480p';
			item['format'] = 'mp4';
			item['url'] = video_links[i].href;
			results.push(cloneObject(item));
		}else if(video_links[i].innerHTML.search('720p') != -1/* && video_links[i].innerHTML.search('MP4') != -1*/){
			console.log(video_links[i].innerHTML + ", HREF = " + video_links[i].href);
			//item = new Array();
			item['resolution'] = '720p';
			item['format'] = 'mp4';
			item['url'] = video_links[i].href;
			results.push(cloneObject(item));
		}
	}
	
	chrome.extension.sendRequest(results);

}

function cloneObject(obj) {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }
 
    var temp = obj.constructor(); // give temp the original obj's constructor
    for (var key in obj) {
		console.dir(obj[key]);
        temp[key] = cloneObject(obj[key]);
    }
	
    return temp;
}
tango();
