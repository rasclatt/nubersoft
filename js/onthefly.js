	// These functions are used for the show-hide of files in admin tools tables
	function ScaleUp(ElemId)
		{
			ElemId.find(":first").css({"z-index":"2","position":"absolute"});
			ElemId.find("> div > img").css({"max-height":"200px","z-index":"2"});
			ElemId.find("> div > .download-img").show();
		}
		
	function ScaleDown(ElemId)
		{
			ElemId.find(":first").css({"z-index":"1","position":"relative"});
			ElemId.find("> div > img").css({"max-height":"50px","z-index":"1"});
			ElemId.find("> div > .download-img").hide();
		}
	
	function ShowHide(parent,child,effect) {
			switch(effect) {
				case ('fade'):
					$(parent).fadeToggle('fast');
					break;
				case ('slide'):
					$(parent).slideToggle('fast');
					break;
				default:
					$(parent).toggle();
			}
		}
	
	function PowerButton(Id,effect,wrapper)
		{
			var IdName	=	$(this).attr('id');
			var IsVisible = $("#"+Id+"_panel").is(":visible");
			// Use slide effect
			if (effect == 'slide') {
				if(IsVisible == true)
					$("#"+Id+"_panel").slideUp('fast');
				else if(IsVisible == false) {
					$(wrapper).slideUp('fast');
					$("#"+Id+"_panel").slideDown('fast');
				}
			}
			else if (effect == 'fade') {
				if(IsVisible == true)
					$("#"+Id+"_panel").fadeOut('fast');
				else if(IsVisible == false) {
					$(wrapper).fadeOut('fast');
					$("#"+Id+"_panel").fadeIn('fast');
				}
			}
			else {
				if(IsVisible == true)
					$("#"+Id+"_panel").slideUp('fast');
				else if(IsVisible == false) {
					$(wrapper).slideUp('fast');
					$("#"+Id+"_panel").slideDown('fast');
				}
			}
		}

	function ScreenPop()
		{
			$("#prefsSlidePanel").fadeIn("fast");
			$("#allHide").fadeOut("fast");
		}
			
    function toggle_visibility(id)
		{
			var e = document.getElementById(id);
			e.style.display = (e.style.display == 'block')? 'none' : 'block';
		}
	
	
	// PROCESS SQL STRINGS
	function onthefly(str, dir, method)
		{
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else {
				// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
				
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("servResponse").innerHTML=xmlhttp.responseText;
				//	tinymce();
				}
			}
			
			xmlhttp.open(method,dir,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		 	xmlhttp.send(str);
		}
	
	function AjaxSimpleCall(targetId,url)
		{
			// Get element by id
			var GetId		=	$(this).attr('id');
			var StingSet	=	'<div class="loading"><h3>Loading...</h3></div>';
			$("#"+targetId).html(StingSet);
			
			$.ajax({
				url: url,
				cache: false,
				success: function() {
						$('#'+targetId).load(url);
					}
			});
		}
		
	function AjaxFlex(targetId,url)
		{
			// Get element by id
			var StingSet	=	'<div class="loading"><h3>Loading...</h3></div>';
			$(targetId).html(StingSet);
			
			$.ajax({
				url: url,
				cache: false,
				success: function(response) {
						$(targetId).html(response);
				}
			});
		}
	
		// Quick check function
		function KeyCheckJax(IdVal,DestId) {
				$.ajax({
					url: '/core.ajax/check.user.php',
					type: 'post',
					data: $('#'+IdVal).serialize(),
					success: function(result) {
							$('#'+DestId).html(result);
						}
				});
			}
		
		function CallAdaptiveAjax(in_url,in_type,in_formname,in_dropid)
			{
				$.ajax({
					url: in_url,
					type: in_type,
					data: $('#'+in_formname).serialize(),
					success: function(response) {
							$('#'+in_dropid).html(response);
						}
				});
			}
		
		function PreventPaste(IdVal)
			{
				$(IdVal).on('paste', function(e){
					e.preventDefault();
					alert("Please type your information");
				});		
			}
			
	function FadeIn(ObjElem,ObjOpts)
		{
			
			SetDisolve		=	(!ObjOpts.disolve)? true : ObjOpts.disolve;
			SetDisolveSpeed	=	(!ObjOpts.disolve_delay)? 2000 : ObjOpts.disolve_delay;
			SetFadeSpeed	=	(!ObjOpts.fade_speed)? 'fast' : ObjOpts.fade_speed;
			SetDelay		=	(!ObjOpts.delay)? 500 : ObjOpts.delay;
			
			ObjElem.delay(SetDelay).fadeIn(SetFadeSpeed);
			
			if(SetDisolve)
				ObjElem.delay(SetDisolveSpeed).fadeOut(SetFadeSpeed);
		}
var	nFX	=	function()
	{
		this.showHide	=	function(obj)
			{
				var acton	=	obj.data('acton');
				var effect	=	obj.data('effect');
				var toggle	=	obj.data('toggle');
				var speed	=	obj.data('speed');
				var hide	=	obj.data('hide');
				
				acton		=	(acton != undefined)? $(acton) : false;
				effect		=	(effect != undefined)? effect : 'fade';
				toggle		=	(toggle != undefined)? ((toggle === 1)? true : false ): true;
				speed		=	(speed != undefined)? speed : 'fast';
				
				if(hide != undefined) {
					$(hide).hide();
				}
				
				if(!acton)
					return false;
				
				switch(effect) {
					case('fade'):
						if(toggle)
							acton.fadeToggle(speed);
						else 
							this.determineView(acton,speed,effect);
						break;
					case('slide'):
						if(toggle)
							acton.slideToggle(speed);
						else 
							this.determineView(acton,speed,effect);
						break;
				}
				
				return this;
			};
		
		this.determineView	=	function(acton,speed,effect)
			{
				switch(effect) {
					case('fade'):
						if(!acton.is("visible"))
							acton.fadeIn(speed);
						break;
					case('slide'):
						if(!acton.is("visible"))
							acton.slideIn(speed);
						break;
				}
				
				return this;
			};
	}


var Dispatcher	=	(function() {
		var instance;
		
		return {
			getInstance: function() {
				if(instance) {
					return instance;
				}

				instance	=	new DispatcherEng();
				
				return instance;
			}
		}
})();
	
var DispatcherEng	=	function()
	{
		var useURL;
		var useFunc;
		var	uFunc;
		
		this.setResp		=	{};
		this.initSuccess	=	{};
		
		this.doAction	=	function(uFunc)
			{
				useFunc	=	uFunc;
				return this;
			};
		
		this.setUrl			=	function(url)
			{
				useURL	=	url;
				return this;
			};
		
		this.beforeSend		=	function(url)
			{
				var html	=	(url == '')? '<div class="loading"><h3>Loading...</h3></div>' : this.fetchHtml(url,'get_loader');
				return html;
			};
		
		this.fetchHtml	=	function(url,action)
			{	
				$.ajax({
						url: url,
						type: 'post',
						data: { action: action },
						success: function(response){
								return response;
							}
				});
				
				return this;
			};
		
		this.simpleAjax	=	function(params)
			{
				$.ajax({
						url: useURL,
						type: "post",
						data: params.data,
						success: function(response) {
								
								if(useFunc != undefined) {
									useFunc(response);
								}
								else {
									switch(params.setHandle) {
										case ('html'):
											if(params.setReturn != undefined)
												$(params.setReturn).html(response);
											break;
										case ('json'):
											jSON	=	JSON.parse(response);
											break;
										default:
											if(params.error_reporting != undefined) {
												if(params.error_reporting)
													console.log(response);
											}
									}
								}
							},
						error:	function(response) {
							if(response == undefined || response == null)
								console.log("failed");
							else
								console.log(response);
						}
					});
			};
		
		this.ajax	= function (obj,opts)
			{
				var setAction		=	(obj.data("action") != undefined)? obj.data("action") : false;
				var setVals			=	(obj.data("vars") != undefined)? obj.data("vars") : false;
				var setReturn		=	(obj.data("sendto") != undefined)? obj.data("sendto") : false;
				var setHandle		=	(obj.data("returned") != undefined)? obj.data("returned") : false;
				var setData			=	(obj.data("senddata") != undefined)? obj.data("senddata") : false;
				var setDataHandle	=	(obj.data("senddataas") != undefined)? obj.data("senddataas") : false;
				var setUse			=	(obj.data("use") != undefined)? obj.data("use") : false;
				var setPlugin		=	(obj.data("plugin") != undefined)? obj.data("plugin") : false;
				var	jSON;
				var	sendData		=	{
											action: setAction,
											use: setUse,
											plugin: setPlugin,
											data: setData,
											vars: setVals,
											send_as: setDataHandle
										};
				if(opts	== undefined)
					opts	=	{};
				
				$.ajax({
						url: useURL,
						type: "post",
						data: sendData,
						success: function(response) {
							
								if(useFunc != undefined) {
									useFunc(response);
								}
								else {
									switch(setHandle) {
										case ('html'):
											if(setReturn != undefined)
												$(setReturn).html(response);
											break;
										case ('json'):
											jSON	=	JSON.parse(response);
											break;
									}
								}
								
								if(opts.error_reporting != undefined) {
									if(opts.error_reporting)
										console.log(response);
								}
							},
						error:	function(response) {
							if(response == undefined || response == null)
								console.log("failed");
							else
								console.log(response);
						}
					});
				
				return this;
			};
		
		this.formData	=	function(obj,defMsg)
			{	
				// Save form to formdata
				var formData	=	new FormData(obj);
				// Save form to jQuery obj
				var findForm	=	$(obj);
				// Find the file input
				var FileInput	=	findForm.find("input[type=file]");
				// Assign the loader dropzone
				var thisLoader	=	$("#nbr_loader");
				// Get the url from the form
				var	ajaxURL		=	findForm.attr('action');
				// Get the message(s) if there are any
				var	msgObj		=	findForm.find('input[name=nbr_msg]').val();
				// Parse the messages
				msgVal			=	(msgObj != undefined)? JSON.parse(msgObj) : { success: "Success", fail: "Failed" };
				var uSuccess	=	msgVal.success;
				var uFail		=	msgVal.fail;
				// Set default message
				defMsg			=	(defMsg == undefined)? { invalid: uFail, empty: 'File cannot be empty' } : defMsg;
				// Assign default response
				var	response	=	{};
				var sendBack;
				this.setResp	=	sendBack;
				var thisDisp	=	this;
				// Send ajax request
				$.ajax({
					beforeSend:	function() {
						if(thisLoader != undefined) {
							thisLoader.html(thisDisp.beforeSend(''));
						}
							
						// Overwrite itself
						FileInput.replaceWith(FileInput.clone(true));
					},
					type: 'POST',
					url: ajaxURL,
					async: false,
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(data){
						// Assign the return for outter processing
						sendBack	=	{ raw: data, json: {} };
						
						console.log(data);
						if(data != '') {
							response	=	JSON.parse(data);
							thisDisp.initSuccess(response);
						}
						
						if(response.valid != undefined) {
							if(!response.valid) {
								alert(defMsg.invalid);
							}
						}

						if(thisLoader)
							$("#nbr_loader").html("");
						
						if(response.nbr_dropspot != undefined) {
							if(response.valid)
								$(response.nbr_dropspot).html(uSuccess);
						}
					},
					error: function(data){
						console.log("error");
						console.log(data);
						alert(uFail);
					}
				});
				
				return this;
			}
	}

var	FilesFoldersEditor	=	function()
	{
		var	obj;
		var	grpObj;
		var	msgDrop;
		
		var	check_all_obj;
		var	messaging_obj;
		var	message_drop_obj;
		
		var mChecked	=	false;
		var mCheckedMsg	=	"Check All";
		
		this.remove_disabled	=	$("input[type=submit]").removeAttr('disabled');
		
		this.setCheckAll	=	function(obj)
			{
				check_all_obj	=	obj;
				return this;
			}
		
		this.checkAll		=	function(grpObj,msgDrop)
			{
				messaging_obj		=	grpObj;
				message_drop_obj	=	msgDrop;
				
				check_all_obj.click(function() { 
					var IsChecked	=	check_all_obj.prop('checked');
					
					if (IsChecked) {
						mChecked	=	true;
						mCheckedMsg	=	"Clear All?";
					}
					
					messaging_obj.prop('checked',mChecked);
					message_drop_obj.html(mCheckedMsg);
				});
				
				return this;
			}
		
		this.propigateCheck	=	function()
			{
				messaging_obj.click(function(){
					var	thisObj		=	$(this);
					var is_checked	=	thisObj.prop('checked');
					
					if (!is_checked) {
						check_all_obj.prop('checked',false);
						message_drop_obj.html(mCheckedMsg);
						
						var FolderId	=	thisObj.data("parentfolder");
						$('#'+FolderId).prop('checked',false);
					}
				});
			}
		
		this.keyUpListener	=	function(objCont)
			{
				$(document).keyup(function(e) {
					if (e.keyCode == 27) {
						objCont.fadeOut("fast");
						$("html").removeClass("no-scroll");
					}
				});
			}
		
		this.formAjaxer	=	function(obj,url)
			{
				obj.submit(function(e) {
					// Stop action from happening
					e.preventDefault();
					// Create new dispatcher
					var dFileEngine	=	Dispatcher.getInstance();
					// Send simple Ajax
					dFileEngine.setUrl(url).simpleAjax({
						data: $(this).serialize(),
						setHandle: 'html',
						setReturn: "#success",
						error_reporting: true
					});
				});
			}
	}

$(document).ready(function(){
		// Enable form
		$('.disabled-submit').removeAttr('disabled');
		var dispUrl	=	'/core.ajax/ajax.dispatcher.php';
		var tAreaCont;
		var	tHeight;
		
		$(this).on('focusin',".textarea",function() {
			tHeight		=	'100px';
			tAreaCont	=	$(this).val().split(/\r\n|\r|\n/g);
			if(tAreaCont.length > 1) {
				$(this).animate({ height: (tAreaCont.length * 23)+"px" });
			}
		});
		
		$(".ajaxDispatcher").click(function() {
			var AutoDispatch	=	Dispatcher.getInstance();
			AutoDispatch.setUrl(dispUrl).ajax($(this),{ error_reporting: true });
		});
		
		// Track Editor ajax functions
		$(".cDispatcher").click(function() {
			var AutoDispatch	=	Dispatcher.getInstance();
				var thisComp	=	$(this).next().find(".templatePopup");
				var dropIt		=	thisComp.find(".component_loader");
				
				if(!thisComp.is("visible"))
					thisComp.fadeIn();
				
			AutoDispatch.doAction(function(response){
				$(dropIt).html(response);
			});
			
			AutoDispatch.setUrl(dispUrl).ajax($(this));//,{ error_reporting: true }
		});
		
		// Component preview window
		$(".nbr_preview_toggle").click(function() {
			$(this).next().fadeToggle('fast');
		});
		// Component admin notes
		$(this).on('click','.nbr_notes',function(v) {
			var prevElem	=	$(this).parents(".componentSetWrap").find(".notes_popup");
			prevElem.fadeToggle('fast');
			console.log();
		});
		$(this).on('click','.notes_popup',function() {
			$(this).fadeToggle();
		});
		
		$(this).on('click',".nCloser",function() {
			var getObj		=	$(this);
			var closeWhat	=	getObj.data("closewhat");
			
			if(closeWhat == undefined) {
				getObj.fadeOut('fast');
			}
			else {
				$(closeWhat).fadeOut('fast');
			}
		});
		
		// General button click which acts upon other elements
		$(this).on('click','.nButton',function(v) {
			var nButtons	=	new nFX();
			var thisButton	=	$(this);
			nButtons.showHide(thisButton);
		});
		
		
		
		// Show errors
		var	ErrorSettings	=	{ disolve: true, disolve_delay: 3200, fade_speed: 'slow' };
		FadeIn($(".nbr_error_msg"),ErrorSettings);
		
		$("#sendtotype").keyup(function() {
			CallAdaptiveAjax('/core.ajax/display.live.type.php','post','text-editor','show-code');
		});
	
		$(".btntrigger").click(function() {
			var thisHdBnt	=	$(this);
			var thisNext	=	thisHdBnt.next();
			if(!thisNext.is(":visible")) {
				$(".panelhide").slideUp('fast');
				thisHdBnt.next().slideDown('fast');
			}
			else {
				thisNext.slideUp('fast');
			}
		});
		
		$(".text-editor-folders").hover(
				function() {
						$(this).find(".text-hovershow").slideDown('fast');
					},
				function() {
						$(this).find(".text-hovershow").slideUp('fast');
					}
			);
		
		PreventPaste('.no-paste');

		$(".usercheck").keyup(function() {
			KeyCheckJax('signupForm','scriptor');
		});
		
		$("input[type=submit]").removeAttr('disabled');
		
		// Make sticky menus
		$(".click_stick").click(function() {
				var ThisButton	=	$(this);
				var StickMenu	=	ThisButton.data('stick');
				var	ClassElem	=	$("."+StickMenu);
				
				if(ClassElem.is(":visible")) {
						ThisButton.slideUp('fast');
						ClassElem.css({"display":"block"});
					}
			});
		
		$(".more-opts-btn").click(function() {
				var ThisButtonActivator	=	$(this).parent();
				var ThisButton	=	ThisButtonActivator.find(".more-opts-panel");
				ThisButton.slideToggle('fast');
			});
		
		
		$(".dragonit").draggable({"cancel":".nondrag"});
					
		$("#activatedropdown").click(function() {
				$("#dropdownmenu").slideToggle("fast");
			});
			
		$("#deleteAJAX_button").click(function() {
				$("#prefsSlidePanel").fadeIn("fast");
				$("#allHide").fadeOut("fast");
			});
			
		$("#prefsSlideToggle").click(function() {
				$("#prefsSlidePanel").fadeIn("fast");
				$("#allHide").fadeOut("fast");
			});
			
		$("#flip").click(function() {
				$("#panel").fadeToggle("fast");
			});
			
		// StackOverflow borrow.... :D
		$(document).delegate('.textarea', 'keydown', function(e) {
				var keyCode = e.keyCode || e.which;
			
				if (keyCode == 9) {
					e.preventDefault();
					var start = $(this).get(0).selectionStart;
					var end = $(this).get(0).selectionEnd;
			
					// set textarea value to: text before caret + tab + text after caret
					$(this).val($(this).val().substring(0, start)
							+ "\t"
							+ $(this).val().substring(end));
			
					// put caret at right position again
					$(this).get(0).selectionStart =
					$(this).get(0).selectionEnd = start + 1;
				}
			});
		
		// Scroll to top of page.
	/*	$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('.scroll-top').fadeIn('slow');
				$('.scroll-top').addClass('add-scroll');
			} else {
				$('.scroll-top').css({"display":"none"});
				$('.scroll-top').removeClass('add-scroll');
			}
		});
		
		// Scroll function
		function ClickScroller(ElemId)
			{
				// scroll-to-top animate
				$(ElemId).click(function () {
					$("html, body").animate({ scrollTop: 0 }, 'slow');
					return false;
				});
			}
		
		ClickScroller('.scroll-top');
	*/
	});