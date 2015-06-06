// Copyright (c) 2012 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

chrome.extension.onRequest.addListener(function(results) {
	console.dir(results);
	html = "";
	for (var i in results) {
		console.dir(results[i]);
		//html += "<tr><td>" +results[i]['resolution'] + "</td><td>" + results[i]['format'] + "</td><td>" + results[i]['url'] + "</td></tr>";
		html += "<tr><td>" +results[i]['resolution'] + "</td><td>" + results[i]['format'] + "</td></tr>";
	}
	document.getElementById("result_table").innerHTML = html;
	
	post_test();
});

// Set up event handlers and inject send_links.js into all frames in the active
// tab.
window.onload = function() {
	chrome.windows.getCurrent(function (currentWindow) {
		chrome.tabs.query({active: true, windowId: currentWindow.id},
						  function(activeTabs) {
		  chrome.tabs.executeScript(
			activeTabs[0].id, {file: 'tango_dancing.js', allFrames: true});
		});
	});
};

function post_test()
{
	$.post("http://v.becktu.com/test.php")
	.done(function(data){
		console.dir(data);
	});
}

