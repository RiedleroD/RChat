var accountid = Number(document.getElementById("logout_btn").dataset.userid);
var post_stuff = document.getElementById("premsg");
var post_length = 0;
var post_receiver = null;//TODO: check if any radio button is checked at page load; some browsers cache that between reloads
var chat_cache_receiver = null;
var chat_cache_since = 0;

function add_form_data(name,value){
	let ip = document.createElement("input");
	ip.name=name;
	ip.value=value;
	ip.hidden=true;
	post_stuff.appendChild(ip);
}

function on_new_paragraph(ev,txt=""){
	post_length+=1;
	let ta = document.createElement("textarea");
	ta.maxLength=128;
	ta.placeholder="Absatz";
	ta.required=true;
	ta.name="element["+post_length+"][txt]";
	ta.value=txt;
	ta.classList.add("in_paragraph");
	post_stuff.appendChild(ta);
	add_form_data("element["+post_length+"][type]","txt");
}
document.getElementById("new_p").onclick=on_new_paragraph;

function on_new_heading(ev,txt=""){
	post_length+=1;
	let ti = document.createElement("input");
	ti.type="text";
	ti.maxLength=64;
	ti.placeholder="Überschrift";
	ti.required=true;
	ti.classList.add("in_head");
	ti.name="element["+post_length+"][txt]";
	ti.value=txt;
	post_stuff.appendChild(ti);
	add_form_data("element["+post_length+"][type]","h");
}
document.getElementById("new_h").onclick=on_new_heading;

function on_new_image(ev,url="",alt=""){
	post_length+=1;
	let iu = document.createElement("input");
	iu.type="url";
	iu.maxLength=128;
	iu.placeholder="Link zur Quelle";
	iu.required=true;
	iu.classList.add("in_imgurl");
	iu.name="element["+post_length+"][src]"
	iu.value=url;
	let ia = document.createElement("input");
	ia.type="text";
	ia.maxLength=128;
	ia.placeholder="Bildtext";
	ia.required=true;
	ia.classList.add("in_imgalt");
	ia.name="element["+post_length+"][alt]"
	ia.value=alt;
	post_stuff.appendChild(iu);
	post_stuff.appendChild(ia);
	add_form_data("element["+post_length+"][type]","img");
}
document.getElementById("new_i").onclick=on_new_image;

function on_preview(ev){
	xhr = new XMLHttpRequest();
	xhr.open("POST","./funcs/post.php?s=preview",false);
	xhr.send(new FormData(document.getElementById("mainform")));
	let pw = document.getElementById("preview");
	pw.innerHTML = xhr.responseText;
}
document.getElementById("previewbtn").onclick=on_preview;

function on_sendmsg(ev){
	let data = new FormData(document.getElementById("mainform"));
	data.append("receiver",post_receiver);
	xhr = new XMLHttpRequest();
	xhr.open("POST","./funcs/post.php?s=send",false);
	xhr.send(data);
	if(xhr.responseText!=""){
		console.log("nonempty sendmsg xhr response:");
		console.log(xhr.responseText);
	}
	document.getElementById("premsg").innerHTML="";
	document.getElementById("preview").innerHTML="";
}
document.getElementById("sendbtn").onclick=on_sendmsg;

function on_addcontact(ev){
	xhr = new XMLHttpRequest();
	xhr.open("POST","./funcs/post.php?s=contact",false);
	xhr.send(new FormData(document.getElementById("contactform")));
	if(xhr.responseText=="null"){
		//contact doesn't exist
	}else{
		let usrid = Number(xhr.responseText);
		if(isNaN(usrid)){
			console.log("unknown addcontact xhr response:");
			console.log(xhr.responseText);
		}else{
			let c = document.getElementById("contacts");
			let cbtn = document.createElement("button");
			cbtn.type="button";
			cbtn.innerText=document.getElementById("contactin").value;
			cbtn.onclick = () => post_receiver=Number(xhr.responseText);
			c.prepend(cbtn);
		}
	}
	document.getElementById("contactin").value="";
}
document.getElementById("newcontactbtn").onclick=on_addcontact;

function reload_chat_loop(){
	xhr = new XMLHttpRequest();
	if(post_receiver!=chat_cache_receiver){//changed chat
		chat_cache_receiver=post_receiver
		chat_cache_since = 0;
		document.getElementById("chatlog").innerHTML="";
	}
	if(post_receiver!=null){
		document.getElementById("chatcard").removeAttribute("style");
		let data = new FormData();
		data.append("receiver",post_receiver);
		data.append("lastknownmessage",chat_cache_since);
		xhr.open("POST","./funcs/post.php?s=fetch",false);
		xhr.send(data);
		let toAppend=[];//post IDs to be appended
		try{
			toAppend=JSON.parse(xhr.responseText);
		}catch(error){
			console.log("error in chat load: ");
			console.log(xhr.responseText);
		}
		if(toAppend.at(-1)>chat_cache_since)
			chat_cache_since = toAppend.at(-1);
		for(let msgid of toAppend){
			xhr = new XMLHttpRequest();
			xhr.open("POST","./funcs/post.php?s=genmsg",false);
			let data = new FormData();
			data.append("msgid",msgid);
			xhr.send(data);
			let chat = document.getElementById("chatlog");
			chat.innerHTML += xhr.responseText;
		}
	}
	setTimeout(reload_chat_loop,500);//non-blocking, waits half a second before calling again
}
setTimeout(reload_chat_loop,500);