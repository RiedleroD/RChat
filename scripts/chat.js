var accountid = Number(document.getElementById("logout_btn").dataset.userid);
var post_stuff = document.getElementById("premsg");
var post_length = 0;
var post_receiver = null;//TODO: check if any radio button is checked at page load; some browsers cache that between reloads
var chat_cache_receiver = null;
var chat_cache_since = 0;
var poll_answercount = 1;

//why do I have to create a basic fucking function like this?
//this should be in the stdlib!
function doPOSTRequest(url, data) {
    return new Promise(function (resolve, reject) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                resolve(xhr.response);
            } else {
                reject({
                    status: this.status,
                    statusText: xhr.statusText
                });
            }
        };
        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        };
        xhr.send(data);
    });
}

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

async function on_new_poll(ev,url="",alt=""){
	post_length+=1;
	let ip = document.createElement("select");
	ip.required=true;
	ip.classList.add("in_poll");
	ip.name="element["+post_length+"][pollid]";
	let placeholder = document.createElement("option");
	placeholder.value="";
	placeholder.setAttribute("hidden","");
	placeholder.setAttribute("disabled","");
	placeholder.setAttribute("selected","");
	placeholder.innerHTML="Select a Poll";
	ip.appendChild(placeholder);
	let polls = JSON.parse(await doPOSTRequest("./funcs/post.php?s=getpolls"));
	for(const [id,title] of Object.entries(polls)){
		let pe = document.createElement("option");
		pe.value=id;
		pe.innerHTML=title;
		ip.appendChild(pe);
	}
	post_stuff.appendChild(ip);
	add_form_data("element["+post_length+"][type]","poll");
}
document.getElementById("new_poll").onclick=on_new_poll;

function on_add_poll_answer(ev){
	let pollform = document.getElementById("pollform");
	let answer = pollform.children[2].cloneNode();
	answer.value="";
	answer.name="answer["+poll_answercount+']';
	answer.placeholder="Antwort "+(poll_answercount+1);
	pollform.insertBefore(answer,pollform.children[pollform.children.length-2]);
	poll_answercount+=1;
}
document.getElementById("addpollanswerbtn").onclick=on_add_poll_answer;

async function on_send_poll(ev){
	let pollform = document.getElementById("pollform");
	console.log(await doPOSTRequest("./funcs/post.php?s=newpoll",new FormData(pollform)));
	pollform.setAttribute('style','visibility:hidden');
	
	let textinputs = document.querySelectorAll("#pollform>input[type='text']");
	for(let i=textinputs.length-1;i>-1;i--){
		if(i==0 || i==1){
			textinputs[i].value="";
		}else{
			textinputs[i].remove();
		}
	}
	document.getElementById("polls").children[0].removeAttribute('style');
	poll_answercount=1;
}
document.getElementById("makepollbtn").onclick=on_send_poll;

async function on_preview(ev){
	let mf = document.getElementById("mainform");
	if(mf.checkValidity()){
		let pw = document.getElementById("preview");
		pw.innerHTML = await doPOSTRequest("./funcs/post.php?s=preview",new FormData(mf));
	}
}
document.getElementById("previewbtn").onclick=on_preview;

async function on_sendmsg(ev){
	let mf = document.getElementById("mainform");
	if(mf.checkValidity()){
		let data = new FormData(mf);
		data.append("receiver",post_receiver);
		let response = await doPOSTRequest("./funcs/post.php?s=send",data);
		if(response!=""){
			console.log("nonempty sendmsg xhr response:");
			console.log(response);
		}
		document.getElementById("premsg").innerHTML="";
		document.getElementById("preview").innerHTML="";
	}
}
document.getElementById("sendbtn").onclick=on_sendmsg;

async function on_addcontact(ev){
	let response = await doPOSTRequest("./funcs/post.php?s=contact",new FormData(document.getElementById("contactform")));
	if(response=="null"){
		//contact doesn't exist
	}else{
		let usrid = Number(response);
		if(isNaN(usrid)){
			console.log("unknown addcontact xhr response:");
			console.log(response);
		}else{
			let c = document.getElementById("contacts");
			let cbtn = document.createElement("button");
			cbtn.type="button";
			cbtn.innerText=document.getElementById("contactin").value;
			cbtn.onclick = () => post_receiver=Number(response);
			c.prepend(cbtn);
		}
	}
	document.getElementById("contactin").value="";
}
document.getElementById("newcontactbtn").onclick=on_addcontact;

async function reload_chat_loop(){
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
		response = await doPOSTRequest("./funcs/post.php?s=fetch",data);
		let toAppend=[];//post IDs to be appended
		try{
			toAppend=JSON.parse(response);
		}catch(error){
			console.log("error in chat load: ");
			console.log(response);
		}
		if(toAppend.at(-1)>chat_cache_since)
			chat_cache_since = toAppend.at(-1);
		for(let msgid of toAppend){
			let data = new FormData();
			data.append("msgid",msgid);
			let chat = document.getElementById("chatlog");
			let response=new DOMParser().parseFromString(
					await doPOSTRequest("./funcs/post.php?s=genmsg",data),
					'text/html').body;
			if(response.children.length==1)
				chat.appendChild(response.children[0]);
			else{
				console.log("invalid message response:");
				console.log(response.outerHTML);
			}
			chat.scrollTo(0,chat.scrollHeight);
		}
	}
	setTimeout(reload_chat_loop,500);//non-blocking, waits half a second before calling again
}
setTimeout(reload_chat_loop,500);