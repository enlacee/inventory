function getPasswordStrength(H){var D=(H.length);if(D>5){D=5}var F=H.replace(/[0-9]/g,"");var G=(H.length-F.length);if(G>3){G=3}var A=H.replace(/\W/g,"");var C=(H.length-A.length);if(C>3){C=3}var B=H.replace(/[A-Z]/g,"");var I=(H.length-B.length);if(I>3){I=3}var E=((D*10)-20)+(G*10)+(C*15)+(I*10);if(E<0){E=0}if(E>100){E=100}return E}var tip_box_has_focus=0;var pw_box_has_focus=0;var attached_form;var pwstrapp;var attached_pwbox={};var password_str_handle_validate=1;var pwminstrength=0;var pwminstrength_fail_txt="Sorry, the password you selected cannot be used because it is too weak and would be too easy to crack.  Please select a password with strength rating of % or higher.";var pwminstrength_tip='You can increase the strength of your password by adding UPPER CASE, numbers, and symbol characters.  You should avoid using words that are in the dictionary as <a href="http://en.wikipedia.org/wiki/Password_cracking" target="_blank">crackers</a> usually start with these first.  Currently the system requires you use a password with a strength rating of % or greater.';function hide_password_tip_panel_if_no_box_focus(){if(!tip_box_has_focus&&!pw_box_has_focus){hide_password_tip_panel()}}function ensurePwStrength(C,A){var B=""+A.value;var D=getPasswordStrength(B);if(D<pwminstrength){YAHOO.util.Event.stopEvent(C);alert(pwminstrength_fail_txt.replace("%",pwminstrength))}}
function updatePasswordStrength_new(G,I,N,R){
	var F=""+G.value;
	if(attached_pwbox[G.id]!=1){
		YAHOO.util.Event.addFocusListener(G,function(V){
				pw_box_has_focus=1});
				YAHOO.util.Event.addBlurListener(G,
					function(V){
						pw_box_has_focus=0;
						setTimeout(hide_password_tip_panel_if_no_box_focus,250)
				});
				attached_pwbox[G.id]=1
	}
	if(pwstrapp&&pwminstrengthapps[pwstrapp]){
		pwminstrength=pwminstrengthapps[pwstrapp]
	}
	if(!attached_form){
		init_passtip_dialog();
		var J=G.form;
		if(J&&J.action&&J.action.length>3){
			if(self.register_validator){
				register_validator("func",function(V){
					var W=V[0];
					var X=""+W.value;
					var Y=getPasswordStrength(X);
					if(Y<pwminstrength){
						return false
					}else{
						return true
					}
				},[G],pwminstrength_fail_txt.replace("%",pwminstrength))
			}else{
				YAHOO.util.Event.addListener(J,"submit",function(V){
					ensurePwStrength(V,G)
				},this,true)
			}
		}
		var Q=document.getElementById("password_tip_panel");
		if(Q){
			YAHOO.util.Event.addBlurListener(Q,function(V){
				tip_box_has_focus=0});
				YAHOO.util.Event.addListener(Q,"click",function(V){
					tip_box_has_focus=1});
					YAHOO.util.Event.addFocusListener(Q,function(V){tip_box_has_focus=1});
					var M=Q.getElementsByTagName("a");
					for(var T=0;T<M.length;T++){
						YAHOO.util.Event.addBlurListener(M[T],function(V){tip_box_has_focus=0});
						YAHOO.util.Event.addListener(M[T],"click",function(V){tip_box_has_focus=1});
						YAHOO.util.Event.addFocusListener(M[T],function(V){tip_box_has_focus=1})
					}
		}
		attached_form=1
	}
	var K=getPasswordStrength(F);
	var P=(parseInt(K/10)*10);
	var E=document.getElementById(I);
	if(!E){
		return ;
		alert("Password Strength Display Element Missing")}
		var O=E.getElementsByTagName("div");
		var A=O[0].getElementsByTagName("div");
		var S=pwminstrength>0?pwminstrength:100;
		var L=K<S?K:S;
		var D=parseInt((L/S)*3);
		A[0].className="pass_bar_base pass_bar_"+P+" pass_bar_color_"+(D?D:1);
		var C=1;
		if(N&&N.text>-1){
			C=N.text
		}
		var B=O[C];
		if(B&&self.pass_strength_phrases){
			if(pwminstrength>50&&K>=50&&K<pwminstrength){
				P=40
			}
			B.innerHTML=pass_strength_phrases[P]+" ("+K+"/100)"
		}
		var U;
		if(N&&N.rating>-1){
			U=N.rating
		}
		var H=O[U];
		if(H&&self.pass_strength_phrases){
			H.innerHTML="Strength: ("+K+")"
		}
		if(K<pwminstrength){
			if(!R){
				show_password_tip_panel()
			}
			if(password_str_handle_validate){
				YAHOO.util.Dom.addClass(G,"formverifyfailed")
			}
		}else{
			hide_password_tip_panel();
			if(password_str_handle_validate){
				YAHOO.util.Dom.removeClass(G,"formverifyfailed")
			}
		}
}
function updatePasswordStrength(F,E,C){var D=""+F.value;var H=getPasswordStrength(D);var I=(parseInt(H/10)*10);var A=document.getElementById(E);if(!A){return ;alert("Password Strength Display Element Missing")}var G=A.getElementsByTagName("div");var B=0;var J=1;if(C&&C.text>-1){J=C.text}if(C&&C.image>-1){B=C.image}var L=G[B];L.id="ui-passbar-"+I;var K=G[J];if(K&&self.pass_strength_phrases){K.innerHTML=pass_strength_phrases[I]}}var password_tip_panel;var password_tip_panel_initted=0;var password_gen_panel;var password_gen_panel_initted=0;var password_use_panel;var password_use_panel_initted=0;var password_gen_pwbox;var password_gen_update_func;var did_password_gen=0;var chrsets={uppercase:[{start:65,end:90}],lowercase:[{start:97,end:122}],numbers:[{start:48,end:57}],symbols:[{start:33,end:47},{start:58,end:64},{start:123,end:126}]};var defaultallowedtxt=["lowercase","uppercase","numbers","symbols"];function get_chr_string(D){var A="";if(!chrsets[D]||!chrsets[D].length){return""}for(var C=0;C<chrsets[D].length;C++){for(var B=chrsets[D][C]["start"];B<=chrsets[D][C]["end"];B++){A+=String.fromCharCode(B)}}return A}function getrand(A){return Math.floor(Math.random()*A)}function generate_password(A,E,D){var C="";if(!E.length){E=defaultallowedtxt}for(var B=0;B<E.length;B++){C+=get_chr_string(E[B])}var G=D.split("");for(var B=0;B<G.length;B++){C=C.replace(G[B],"")}if(C.length==0){return""}var F="";while(F.length<A){F+=C.charAt(getrand(C.length))}return F}function open_usepass_dialog(A){init_usepass_dialog();document.getElementById("password_use_newpass").innerHTML=html_encode_str(A);password_use_panel.show()}function open_passgen_dialog(B,A){init_passgen_dialog();password_gen_pwbox=A;password_gen_update_func=B;password_gen_panel.show();if(!did_password_gen){dialogGeneratePass()}}function handlePassCancel(){password_gen_panel.hide()}function handlePassSubmit(){password_gen_panel.hide();var E=document.getElementById("dialogPassword");var F=document.getElementById(password_gen_pwbox);F.value=E.value;var A=[F];if(F.type=="password"){var C=0;var B=document.getElementsByTagName("input");for(var D=0;D<B.length;D++){if(C){if(B[D].type=="password"){A.push(B[D]);B[D].value=E.value;break}else{if(B[D].type=="text"){break}}}else{if(B[D].id==password_gen_pwbox){C=1}}}}password_gen_update_func();if(self.do_validate){for(var D=0;D<A.length;D++){if(A[D].form&&A[D].form.id){do_validate(A[D].form.id,0,0,A[D].id)}}}open_usepass_dialog(E.value)}function init_passtip_dialog(){if(password_tip_panel_initted){return }password_tip_panel_initted=1;password_tip_panel=new YAHOO.widget.Panel("password_tip_panel",{width:"300px",fixedcenter:false,constraintoviewport:false,close:true,draggable:true,modal:false,visible:false});password_tip_panel.setBody(pwminstrength_tip.replace("%",pwminstrength));var A=document.getElementById("sdiv");if(!A){A=document.body}password_tip_panel.render(A);password_tip_panel.hide();document.getElementById("password_tip_panel").style.display=""}function closeUsePass(){password_use_panel.hide()}function init_usepass_dialog(){if(password_use_panel_initted){return }password_use_panel_initted=1;password_use_panel=new YAHOO.widget.Dialog("password_use_panel",{width:"400px",fixedcenter:true,constraintoviewport:true,close:true,draggable:false,modal:false,buttons:[{text:"Close",handler:closeUsePass,isDefault:true}],visible:false});var A=document.getElementById("sdiv");if(!A){A=document.body}password_use_panel.render(A);password_use_panel.hide();document.getElementById("password_use_panel").style.display=""}function init_passgen_dialog(){if(password_gen_panel_initted){return }password_gen_panel_initted=1;password_gen_panel=new YAHOO.widget.Dialog("password_gen_panel",{width:"400px",fixedcenter:true,constraintoviewport:true,close:true,draggable:true,modal:false,buttons:[{text:"Use Password",handler:handlePassSubmit,isDefault:true},{text:"Cancel",handler:handlePassCancel}],visible:false});var A=document.getElementById("sdiv");if(!A){A=document.body}password_gen_panel.render(A);password_gen_panel.hide();document.getElementById("password_gen_panel").style.display=""}function handle_hide_passtip(){if(password_tip_panel.cfg.getProperty("visible")){password_tip_panel.hide()}}function hide_password_tip_panel(){handle_hide_passtip()}function handle_hide_passgen(){}function show_password_tip_panel(){var E=document.getElementById("password");var B=YAHOO.util.Region.getRegion(E);var C=document.getElementById("passwdGen");if(C){var A=YAHOO.util.Region.getRegion(C);if(A.bottom>B.bottom){B.bottom=A.bottom}}password_tip_panel.moveTo(B.right+5,B.bottom+10);if(!password_tip_panel.cfg.getProperty("visible")){password_tip_panel.show();if(E){try{E.focus()}catch(D){}}}}function dialogGeneratePass(){did_password_gen=1;var C=document.getElementById("dialogPassword");var F=document.getElementById("pwlength");var A=parseInt(F.value);if(!A||A<8){A=8}C.setAttribute("size",A);for(var B=0;B<10;B++){C.value=generate_password(A,[document.getElementById("uppercase").checked?"uppercase":"",document.getElementById("lowercase").checked?"lowercase":"",document.getElementById("numbers").checked?"numbers":"",document.getElementById("symbols").checked?"symbols":""],"'oO0\"");var D=C.value+"";var E=getPasswordStrength(D);if(E>=100){break}}updatePasswordStrength_new(C,"Dialog_passwdRating",{text:2,rating:3},1);password_gen_panel.show()}function html_encode_str(A){return A.replace(/\&/g,"&amp;").replace(/\</g,"&lt;").replace(/\>/g,"&gt;").replace(/\"/g,"&quot;")};