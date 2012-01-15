function GetXmlHttpObject()
{
  var xmlHttp=null;
  try
    {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    }
  return xmlHttp;
}

function doAjaxCall(url, callback, method, mode) {
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null) {
  		alert ("Your browser does not support AJAX!");
  		return;
  	} 
	
	xmlHttp.onreadystatechange= eval(callback);
	xmlHttp.open(method, url, mode);
	xmlHttp.send(null);
}

function updateMain() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("main").innerHTML=xmlHttp.responseText;
		document.getElementById("info").innerHTML= "";
	}
}

function updateInfo() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("info").innerHTML=xmlHttp.responseText;
	}
}

function updateHint() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("hint").innerHTML=xmlHttp.responseText;
	}
}

function updateCaption() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("caption").innerHTML=xmlHttp.responseText;
	}
}

function updateTag() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("tag").innerHTML=xmlHttp.responseText;
	}
}

function updateContainer() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("container").innerHTML=xmlHttp.responseText;
	}
}

function updateComment() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("comment_box").innerHTML = xmlHttp.responseText;
		document.getElementById("new_comment").value = ""; 
	}
}

function updateManageTags() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("mtag").innerHTML = xmlHttp.responseText;
	}
}

function updateSend() { 
	if (xmlHttp.readyState==4) { 
		document.getElementById("send").innerHTML = xmlHttp.responseText;
	}
}

function createAlbum(aid) {
	html = "<iframe src=\"createAlbum.php?aid=" +aid +"\" style=\"border-style:none;width:80%;height:80%\" /></iframe>";
	document.getElementById("main").innerHTML = html;
	document.getElementById("info").innerHTML= "";
}

//grab and display photos from album with id
function showPhotos(id) {
	url = "showPhotos.php?id=" + id;
	doAjaxCall(url, "updateMain", "GET", true);
}

//display photo, pid is the photo id and aid is the parent album id
function showPhoto(pid) {
	url = "showPhoto.php?pid=" + pid;
	doAjaxCall(url, "updateMain", "GET", true);
}

//display photo in a search context
function showSearchPhoto(pid, uid, term, scope) {
	url = "showSearchPhoto.php?pid=" + pid;
	url+= "&uid=" + uid;
	url+= "&term=" + term;
	url+= "&scope=" + scope;
	doAjaxCall(url, "updateMain", "GET", true);
}

//display album information
function showAlbumInfo(id) {
	url = "showAlbumInfo.php?id=" + id;
	doAjaxCall(url, "updateInfo", "GET", true);
}

//display photo information
function showPhotoInfo(id) {
	url = "showPhotoInfo.php?id=" + id;
	doAjaxCall(url, "updateInfo", "GET", true);
}

function showHint(flag) {
	var hint = "";
	if (flag == 1) {
		hint = "<i>my albums</i>";
	}
	else if (flag == 2) {
		hint = "<i>create a new album and upload photos</i>";
	} 
	else if (flag == 3) {
		hint = "<i>upload photos, delete albums, share albums ...</i>";
	}
	else if (flag == 4) {
		hint = "<i>change your password or email</i>";
	}
	else if (flag == 5) {
		hint = "<i>perform tag based search</i>";
	}
	else if (flag == 6) {
		hint = "<i>my shared albums</i>";
	}
	else if (flag == 7) {
		hint = "<i>system overview</i>";
	}
	else if (flag == 8) {
		hint = "<i>find a user by email</i>";
	}
	else if (flag == 9) {
		hint = "<i>configure system password</i>";
	}
	document.getElementById('hint').innerHTML = hint;
}

//show and input box in element with id, box
function inputBox(id, box) {
	var html = "";
	if (box == "caption") {
		html = '<input class="updates" type="text" id="cap" value="Enter new caption here" ' +
		 		'onClick="javascript:document.getElementById(\'cap\').value=\'\'" size="70" />' +
		'<input type="button" class="options" value="update" onClick="changeCaption(\''  +id +'\')" />';
	}
	else if (box == "tag") {
		 html = '<input class="updates" type="text" id="newtag" value="Enter new tag here" ' +
		 		'onClick="javascript:document.getElementById(\'newtag\').value=\'\'" size="70" />' +
		'<input type="button" class="options" value="update" onClick="tag(\''  +id + '\')" />';
	}
	else if (box == "send") {
		 html = '<input class="updates" type="text" id="email" value="Enter recipient\'s email here" ' +
		 		'onClick="javascript:document.getElementById(\'email\').value=\'\'" size="70" />' +
		'<input type="button" class="options" value="send" onClick="sendPhoto(\''  +id + '\')" />';
	}
	else if (box == "search") {
		html = '<input class="updates" type="text" id="term" value="Enter search term here" ' +
		 		'onClick="javascript:document.getElementById(\'term\').value=\'\'" size="40" /> ' +
'my albums <input type="radio" name="scope" class="options" onClick="javascript:document.getElementById(\'scope\').value=0" /> ' +
'universal <input type="radio" name="scope" class="options" onClick="javascript:document.getElementById(\'scope\').value=1" /> ' +
			'<input type="hidden" id="scope" value="1" />' +
		'<input type="button" class="options" value="go" onClick="search()" />';
	}
	else if (id == "search_user") {
		html = '<input class="updates" type="text" id="term" value="Enter user email here" ' +
		 		'onClick="javascript:document.getElementById(\'term\').value=\'\'" size="40" /> ' +
		'<input type="button" class="options" value="go" onClick="userSearch()" />';
		box = "search";
	}
	//nasty hack to enable compatibility with shareAlbum
	else if (id == "share") {
		html = '<input class="updates" type="text" id="newmember" size="30" value="Enter email address here" ' +
		 		'onClick="javascript:document.getElementById(\'newmember\').value=\'\'" size="70" />' +
		'<input type="button" class="options" value="go" onClick="shareAlbum(\''  +box + '\')" />';
	}
	else {
		html = '<input class="updates" type="text" id="newname" size="30" value="Enter new name here" ' +
		 		'onClick="javascript:document.getElementById(\'newname\').value=\'\'" size="70" />' +
		'<input type="button" class="options" value="update" onClick="renameAlbum(\''  +id + '\')" />';
	}
	document.getElementById(box).innerHTML = html;
}

//change caption of photo with id
function changeCaption(id) {
	url = "changeCaption.php?id=" + id +"&txt=" + document.getElementById('cap').value;
	doAjaxCall(url, "updateCaption", "GET", true);
}

//delete photo with id
function deletePhoto(id) {
	url = "deletePhoto.php?id=" + id;
	doAjaxCall(url, "updateMain", "GET", true);
}

//tag photo with id
function tag(id) {
	url = "tag.php?id=" + id + "&tag=" + document.getElementById('newtag').value;
	doAjaxCall(url, "updateTag", "GET", true);
}

//delete tag from photo with id
function deleteTag(id, tag) {
	url = "deleteTag.php?id=" + id + "&tag=" + tag;
	doAjaxCall(url, "updateManageTags", "GET", true);
}

//show album management view of logged in user
function manageAlbums() {
	url = "manageAlbums.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

//delete album with id
function deleteAlbum(id) {
	url = "deleteAlbum.php?id=" + id;
	doAjaxCall(url, "updateMain", "GET", true);
}

//rename album with id
function renameAlbum(id) {
	url = "renameAlbum.php?id=" + id +"&name="  +document.getElementById('newname').value;
	doAjaxCall(url, "updateMain", "GET", true);
}

//display input boxes in studio depending on fn
//id is photo id, src is photo source
function showStudioParams(fn, id, src) {
	//define colors

	colors = "<select class=\"updates\" id=\"colors\">" +
					"<option value=\"FFFFFF\" style=\"background-color: White;color: #FFFFFF;\">White</option>" +
					"<option value=\"000000\" style=\"background-color: Black;color: #000000;\">Black</option>" +
					"<option value=\"808080\" style=\"background-color: Gray;color: #808080;\">Gray</option>" +
					"<option value=\"A9A9A9\" style=\"background-color: DarkGray;color: #A9A9A9;\">Dark Gray</option>" +
					"<option value=\"D3D3D3\" style=\"background-color: LightGrey;color: #D3D3D3;\">Light Gray</option>" +
					"<option value=\"7FFFD4\" style=\"background-color: Aquamarine;color: #7FFFD4;\">Aquamarine</option>" +
					"<option value=\"40E0D0\" style=\"background-color: Turquoise;color: #40E0D0;\">Turquoise</option>" +
					"<option value=\"87CEEB\" style=\"background-color: SkyBlue;color: #87CEEB;\">Sky Blue</option>" +
					"<option value=\"0000FF\" style=\"background-color: Blue;color: #0000FF;\">Blue</option>" +
					"<option value=\"000080\" style=\"background-color: Navy;color: #000080;\">Navy</option>" +
					"<option value=\"483D8B\" style=\"background-color: DarkSlateBlue;color: #483D8B;\">Dark Slate Blue</option>" +
					"<option value=\"800080\" style=\"background-color: Purple;color: #800080;\">Purple</option>" +
					"<option value=\"FF1493\" style=\"background-color: DeepPink;color: #FF1493;\">Deep Pink</option>" +
					"<option value=\"EE82EE\" style=\"background-color: Violet;color: #EE82EE;\">Violet</option>" +
					"<option value=\"FFC0CB\" style=\"background-color: Pink;color: #FFC0CB;\">Pink</option>" +
					"<option value=\"006400\" style=\"background-color: DarkGreen;color: #006400;\">Dark Green</option>" +
					"<option value=\"008000\" style=\"background-color: Green;color: #008000;\">Green</option>" +
					"<option value=\"9ACD32\" style=\"background-color: YellowGreen;color: #9ACD32;\">Yellow Green</option>" +
					"<option value=\"7CFC00\" style=\"background-color: LawnGreen;color: #7CFC00;\">Lawn Green</option>" +
					"<option value=\"6B8E23\" style=\"background-color: OliveDrab;color: #6B8E23;\">Olive Drab</option>" +
					"<option value=\"FFFF00\" style=\"background-color: Yellow;color: #FFFF00;\">Yellow</option>" +
					"<option value=\"FFA500\" style=\"background-color: Orange;color: #FFA500;\">Orange</option>" +
					"<option value=\"FF0000\" style=\"background-color: Red;color: #FF0000;\">Red</option>" +
					"<option value=\"B03060\" style=\"background-color: #B03060;color: #B03060;\">Maroon</option>" +
					"<option value=\"A52A2A\" style=\"background-color: Brown;color: #A52A2A;\">Brown</option>" +
					"<option value=\"DEB887\" style=\"background-color: BurlyWood;color: #DEB887;\">Burly Wood</option>" +
					"<option value=\"F5F5DC\" style=\"background-color: Beige;color: #F5F5DC;\">Beige</option>" +
					"<option value=\"FFDAB9\" style=\"background-color: PeachPuff;color: #FFDAB9;\">Peach Puff</option>" +
					"<option value=\"E6E6FA\" style=\"background-color: Lavender;color: #E6E6FA;\">Lavender</option>" +
					"<option value=\"8B4513\" style=\"background-color: #8B4513;color: #8B4513;\">Chocolate</option>" +
			"</select>";	
	colors2 = colors.replace(/colors/, "colors2");


	html = "";
	if (fn == "scale") {
		html = '<span>' +
				'percent: <input class="updates" type="text" id="pct" size="4" maxlength="4" /> ' +
				'<input type="button" class="options" value="process" onClick="process(\'scale\', \'' +id +'\')" />' +
			'</span>';
	}
	else if (fn == "resize") {
		html = '<span>' +
				'width: <input class="updates" type="text" id="w" size="4" maxlength="4" /> ' +
				'height: <input class="updates" type="text" id="h" size="4" maxlength="4" /> ' + 
				'<input type="button" class="options" value="process" onClick="process(\'resize\', \'' +id +'\')" />' +
			'</span>';
	}
	else if (fn == "round") {
		html = '<span>' +
				'radius: <input class="updates" type="text" id="radius" size="4" maxlength="4" /> ' +
				'background: ' +colors + 
				'<input type="button" class="options" value="process" onClick="process(\'round\', \'' +id + '\')" />' +
			'</span>';
	}
	else if (fn == "rframe") {
		html = '<span>' +
				'radius: <input class="updates" type="text" id="rradius" size="4" maxlength="4" /> ' +
				'thickness: <input class="updates" type="text" id="rthickness" size="4" maxlength="4" /> ' +
				'color: ' +colors + 
				' background: ' +colors2 +
				'<input type="button" class="options" value="process" onClick="process(\'rframe\', \'' +id + '\')" />' + 
			'</span>';
	}
	else if (fn == "frame") {
		html = '<span>' +
				'thickness: <input class="updates" type="text" id="thickness" size="4" maxlength="4" /> ' +
				'color: ' +colors + 
				'<input type="button" class="options" value="process" onClick="process(\'frame\', \'' +id +'\')" />' +
			'</span>';
	}
	else if (fn == "rotate") {
		html = '<span>' +
				'angle: <input class="updates" type="text" id="angle" size="4" maxlength="4" /> ' +
				'background: ' +colors + 
				'<input type="button" class="options" value="process" onClick="process(\'rotate\', \'' +id + '\')" />' +
				'</span>';
	}

	document.getElementById('params').innerHTML = html;
}

//show photo editing studio
function showStudio(id) {
	url = "showStudio.php?pid=" + id;
	doAjaxCall(url, "updateMain", "GET", true);
}

//process requests from studio
function process(fn, id) {
	rgb = "";
	rgb2 = "";
	if (document.getElementById('colors')) {
		rgb = document.getElementById('colors').value;
	}
	if (document.getElementById('colors2')) {
		rgb2 = document.getElementById('colors2').value;
	}
	src = document.getElementById('src').value;
	url = "";
	if (fn == "scale") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src + "&pct=" + document.getElementById('pct').value;
	}
	else if (fn == "resize") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src + "&w=" + document.getElementById('w').value;
		url+= "&h=" + document.getElementById('h').value;
	}
	else if (fn == "rotate") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src +"&angle=" +document.getElementById('angle').value +"&rgb=" + rgb;
	}
	else if (fn == "round") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src +"&radius=" +document.getElementById('radius').value +"&rgb=" + rgb;
	}
	else if (fn == "rframe") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src +"&rradius=" +document.getElementById('rradius').value +"&rgb=" + rgb;
		url+= "&rthickness=" +document.getElementById('rthickness').value  + "&rgb2=" + document.getElementById('colors2').value;
	}
	else if (fn == "frame") {
		url = "process.php?pid=" + id + "&fn=" + fn + "&src=" + src +"&thickness=" +document.getElementById('thickness').value +"&rgb=" + rgb;
	}

	doAjaxCall(url, "updateMain", "GET", true);
}

//save photo from studio
function save(id) {
	url = "save.php?pid=" + id +"&src=" + document.getElementById('src').value;
	doAjaxCall(url, "updateHint", "GET", true);
}

//user with id performs search on term
//scope = 0 => local, 1=> universal
function search() {
	url = "search.php?term="  +document.getElementById('term').value;
	url+= "&scope=" + document.getElementById('scope').value;
	document.getElementById('search').innerHTML = "";
	doAjaxCall(url, "updateMain", "GET", true);
}

function userSearch() {
	url = "searchUser.php?term="  +document.getElementById('term').value;
	document.getElementById('search').innerHTML = "";
	doAjaxCall(url, "updateMain", "GET", true);
}


function tagSearch(tag) {
	url = "search.php?&term="  +tag;
	url+= "&scope=1";
	doAjaxCall(url, "updateMain", "GET", true);
}

//show info of photo with id from a search perspective
function showSearchInfo(id) {
	url = "showSearchInfo.php?id=" + id;
	doAjaxCall(url, "updateInfo", "GET", true);
}

//add a comment to photo with id
function addComment(id) {
	url = "addComment.php?id=" + id + "&text=" + document.getElementById('new_comment').value;
	doAjaxCall(url, "updateComment", "GET", true);
}

//delete comment with cid on photo with id
function deleteComment(cid, id) {
	url = "deleteComment.php?id=" + id + "&cid=" +cid;
	doAjaxCall(url, "updateComment", "GET", true);
}

//show albums of logged in user
function showAlbums() {
	url = "showAlbums.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function login() { 
	url = "login.php?uid=" + document.getElementById('uid').value;
	url+= "&passwd=" + document.getElementById('passwd').value;
	doAjaxCall(url, "updateContainer", "GET", true);
}

function managePhoto(id) {
	url = "managePhoto.php?id=" + id;
	doAjaxCall(url, "updateMain", "GET", true);
}

function showPublicAlbums(uid) {
	url = "showPublicAlbums.php?uid=" + uid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function showPublicPhotos(aid) { 
	url = "showPublicPhotos.php?aid=" + aid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function showPublicPhoto(pid) {
	url = "showPublicPhoto.php?pid=" + pid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function sendPhoto(pid) { 
	url = "sendPhoto.php?pid=" + pid;
	url+= "&email="+ document.getElementById('email').value;
	doAjaxCall(url, "updateSend", "GET", true);
}

function shareAlbum(aid) { 
	url = "shareAlbum.php?aid=" + aid;
	url+= "&email=" + document.getElementById('newmember').value;
	doAjaxCall(url, "updateMain", "GET", true);	
	document.getElementById(aid).innerHTML = "";
}

function unshareAlbum(aid) { 
	url = "unshareAlbum.php?aid=" + aid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function showSharedAlbums() {
	url = "showSharedAlbums.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminShowUsers() { 
	url = "adminShowUsers.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminShowAlbums() { 
	url = "adminShowAlbums.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminShowPhotos() { 
	url = "adminShowPhotos.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminShowUserAlbums(uid) { 
	url = "adminShowUserAlbums.php?uid=" + uid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminShowUserDetails(uid) { 
	url = "adminShowUserDetails.php?uid=" + uid;
	doAjaxCall(url, "updateMain", "GET", true);
}

function saveUserEmail(uid) { 
	url = "saveUserEmail.php?uid=" + uid;
	url+= "&email=" + document.getElementById("email").value;
	doAjaxCall(url, "updateMain", "GET", true);
	document.getElementById("hint").innerHTML = "email address updated successfully";
}

function saveUserPassword(uid) { 
	url = "saveUserPassword.php?uid=" + uid;
	url+= "&password=" + document.getElementById("password").value;
	
	doAjaxCall(url, "updateMain", "GET", true);
	document.getElementById("hint").innerHTML = "password updated successfully";
}

function adminDeleteUser(uid) {
	if (confirm("Are you sure you want to delete this user?")) {	 
		url = "adminDeleteUser.php?uid=" + uid;
		doAjaxCall(url, "updateMain", "GET", true);
		document.getElementById("hint").innerHTML = "user deleted successfully";
	}
}

function adminShowNewUserDetails() {
	url = "adminShowNewUserDetails.php";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminSaveNewUserDetails() { 
	uid = document.getElementById("email").value;	
	password = document.getElementById("password").value;	
	fname = document.getElementById("fname").value;
	lname = document.getElementById("lname").value;
	
	if (uid && password && fname && lname) {
		url = "adminSaveNewUserDetails.php?uid=" + uid;
		url+= "&password=" + password;
		url+= "&fname=" + fname;
		url+= "&lname=" + lname;	
		doAjaxCall(url, "updateMain", "GET", true);
		document.getElementById("hint").innerHTML = "New user created successfully";
	} else {
		alert("All fields are required");
	}
}

function deleteUserFromSharedAlbum(uid, aid) {
	if (confirm("Are you sure you want to revoke this user's access to your album")) {
		url = "deleteUserFromSharedAlbum.php?uid=" + uid;
		url+= "&aid=" + aid;
		doAjaxCall(url, "updateMain", "GET", true);
	}
}

function adminShowPassword() {
	url = "adminShowUserDetails.php?admin=1";
	doAjaxCall(url, "updateMain", "GET", true);
}

function adminSetPassword() {
	url = "saveUserPassword.php?password=" + document.getElementById('passwd').value;
	doAjaxCall(url, "updateMain", "GET", true);
}

function setPassword(uid) {
	if (document.getElementById('passwd').value == '') {
		alert("Password may not be blank");
	} else {	
		url = "saveUserPassword.php?user=1&password=" + document.getElementById('passwd').value + "&uid=" + uid;
		doAjaxCall(url, "", "GET", true);
		document.getElementById("hint").innerHTML = "Password reset successfully";
	}
}

function setEmail(uid) {
	if (document.getElementById('email').value == '') {
		alert("Email may not be blank");
	} else {	
		url = "saveUserEmail.php?user=1&email=" + document.getElementById('email').value + "&uid=" + uid;
		doAjaxCall(url, "", "GET", true);
		document.getElementById("hint").innerHTML = "Email reset successfully";
	}
}
