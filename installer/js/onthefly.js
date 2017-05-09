	
	$('.disabled-submit').removeAttr('disabled');
	
	// These functions are used for the show-hide of files in admin tools tables
	function ScaleUp(ElemId) {
			ElemId.find(":first").css({"z-index":"2","position":"absolute"});
			ElemId.find("> div > img").css({"max-height":"200px","z-index":"2"});
			ElemId.find("> div > .download-img").show();
		}
		
	function ScaleDown(ElemId) {
			ElemId.find(":first").css({"z-index":"1","position":"relative"});
			ElemId.find("> div > img").css({"max-height":"50px","z-index":"1"});
			ElemId.find("> div > .download-img").hide();
		}
	
	function ShowHide(parent,child,effect) {
			if(effect == 'fade') {
					$(parent).fadeToggle('fast');
				}
			else if(effect == 'slide') {
					$(parent).slideToggle('fast');
				}
			else {
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
			
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block') {
          e.style.display = 'none'; 
		  }
       else {
          e.style.display = 'block';
		  }
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
			
		//	document.write(dir);
		//	document.write(str);
			
			xmlhttp.open(method,dir,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		 	xmlhttp.send(str);
		}
	
	// GENERAL HIDING	
	function MM_changeProp(objId,x,theProp,theValue) { //v9.0
	  var obj = null; with (document){ if (getElementById)
	  obj = getElementById(objId); }
	  if (obj){
		if (theValue == true || theValue == false)
		  eval("obj.style."+theProp+"="+theValue);
		else eval("obj.style."+theProp+"='"+theValue+"'");
	  }
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
					url: '/ajax/check.user.php',
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
		
		function PreventPaste(IdVal) {
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
	
	var Dispatcher	=	{
			
			beforeSend: function(url)
				{
					var html	=	(url == '')? '<div class="loading"><h3>Loading...</h3></div>' : this.fetchHtml(url,'get_loader');
				},
			
			fetchHtml:	function(url,action)
				{
					$.ajax({
							url: url,
							type: 'post',
							data: { action: action },
							success: function(response){
									return response;
								}
					});
				},
			
			ajax: function (url,obj,opts) {
				var setAction		=	obj.data("action");
				var setVals			=	obj.data("vars");
				var setReturn		=	obj.data("sendto");
				var setHandle		=	obj.data("returned");
				var setData			=	obj.data("senddata");
				var setDataHandle	=	obj.data("senddataas");
				var	jSON;
				
				$.ajax({
						url: url,
						type: "post",
						data:	{
									action: ((setAction != undefined)? setAction : false),
									data: ((setData != undefined)? setData : false),
									vars: ((setVals != undefined)? setVals : false),
									send_as: ((setDataHandle!= undefined)? setDataHandle : false)
								},
						success: function(response) {
								switch(setHandle) {
									case ('html'):
										if(setReturn != undefined)
											$(setReturn).html(response);
										break;
									case ('json'):
										jSON	=	JSON.parse(response);
										break;
									default:
										if(opts.error_reporting != undefined) {
												if(opts.error_reporting)
													console.log(response);
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
			}
	}
	
$(document).ready(function(){
	
		$(".ajaxDispatcher").click(function() {
			Dispatcher.ajax('/ajax/ajax.dispatcher.php',$(this),{ error_reporting: true });
		});
	
	
		// Show errors
		var	ErrorSettings	=	{ disolve: true, disolve_delay: 3200, fade_speed: 'slow' };
		FadeIn($(".nbr_error_msg"),ErrorSettings);
		
		$("#sendtotype").keyup(function() {
			CallAdaptiveAjax('/ajax/display.live.type.php','post','text-editor','show-code');
		});
	
		$(".btntrigger").click(function() {
			var GetData	=	$(this).data('button');
			$(".panelhide").slideUp('fast');
			$("#"+GetData).slideDown('fast');
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
		$(window).scroll(function () {
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
	});