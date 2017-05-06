/* This script and many more are available free online at
The JavaScript Source!! http://www.javascriptsource.com
Created by: Jean P. May, Jr. | http://www.wideopenwest.com/~thebearmay */

function autoDate () {
	var tDay = new Date();
	var tMonth = tDay.getMonth()+1;
	var tDate = tDay.getDate();
	if ( tMonth < 10) tMonth = "0"+tMonth;
	if ( tDate < 10) tDate = "0"+tDate;
// 	document.getElementById("tDate").value = "/"+tDate+"/"+tDay.getFullYear();
	document.getElementById("AfterDate").value = tDay.getFullYear()+"-"+tMonth+"-"+tDate
	document.getElementById("DateBefore").value = tDay.getFullYear()+"-"+tMonth+"-"+tDate
 }

// Multiple onload function created by: Simon Willison
// http://simonwillison.net/2004/May/26/addLoadEvent/
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

addLoadEvent(function() {
  autoDate();
});
