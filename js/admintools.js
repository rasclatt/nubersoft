(function($)
	{
		var NubeTools	=	function(element) {
			var elem	=	$(element);
			var obj		=	this;
			// Public method
			this.publicMethod = function() {
				console.log('publicMethod() called!');
			};
   		};
		
		$.fn.nuber = function() {
			return this.each(function() {
				var element	=	$(this);
				// Return early if this element already has a plugin instance
				if (element.data('myplugin')) return;
				var nuber	=	new NubeTools(this);
				// Store plugin object in this element's data
				element.data('myplugin', nuber);
			});
		};
	}
)(jQuery);

var WindowEngine	=	{
		
		timeOut: 3,
		
		resetTime: false,
		
		dispDocSize: function(obj)
			{
				var nbrDocSize	=	obj;
				
				if(!nbrDocSize.is(":visible"))
					nbrDocSize.show();
		
				nbrDocSize.html("w:"+$(window).width()+"px");
				
				// Fade out after 2 seconds
				setTimeout(function() { nbrDocSize.fadeOut('fast'); }, 2000);
				
				//this.resetTime	=	true;
			}
	};
	
// This is just a fast slide toggler
function SlideToggler(id)
	{
		$("#"+id).slideToggle("fast");
	}
// This shows errors on trigger
function ShowErrors(ErrorHTML)
	{
		$('#errorWindow').delay(200).slideDown('slow');
		$('#errorWindow').css({"cursor":"pointer"});
		$("#errorWindow").html(ErrorHTML);
		$('#errorWindow').click(function() {
				$('#errorWindow').slideUp('fast');
			});
	}
// Accordion button
function nbrAccordion(ElemObj,wrapper,effect)
	{
		// Assign effect
		effect			=	(!effect)? 'slide':effect;
		// Select the next element to the object
		var	FindElem	=	ElemObj.next();
		// Check if that object is visible or not
		var IsVisible	=	FindElem.is(":visible");
		// If visible
		if(IsVisible)
			// Fade out/Slide up the panel 
			(effect == 'fade')? ElemObj.next().fadeOut('fast') : ElemObj.next().slideUp('fast');
		else {
				// First slide up all elements with the same class named panel
				(effect == 'fade')? wrapper.fadeOut('fast') : wrapper.slideUp('fast');
				// Reveal the selected objects panel
				(effect == 'fade')? ElemObj.next().fadeIn('fast') : ElemObj.next().slideDown('fast');
			}
	}

function nbrRevealFX(ElemObj,wrapper,effect)
	{
		// Assign effect
		effect			=	(!effect)? 'slide':effect;
		// Check if that object is visible or not
		var IsVisible	=	ElemObj.is(":visible");
		// If visible
		if(!IsVisible) {
				// First slide up all elements with the same class named panel
				(effect == 'fade')? wrapper.fadeOut('fast') : wrapper.slideUp('fast');
				// Reveal the selected objects panel
				(effect == 'fade')? ElemObj.fadeIn('fast') : ElemObj.slideDown('fast');
			}
	}

function nbrSideSlide(ObjElem,Width)
	{
		ObjElem.animate({width:'toggle'},Width);
	}
	
function DetermineAJAXDrop(Elem)
	{
		switch (Elem) {
			case ('g'):
				return '#ajax_admindrop';
			case ('e'):
				return '#ajax_loadwindow';
			default:
				return 'body';
		}
	}

function _AJAX(SendTo,ReturnTo)
	{
		ReturnTo.html('<div class="nbr_loader"></div>');
		$.ajax({
				url: SendTo,
				cache: false,
				success: function(response) {
					ReturnTo.html(response);
				}
		});
	}

function close_what(ObjElem)
	{
		var GetData	=	ObjElem.data("closewhat");
		if(GetData == undefined)
			return false;
			
		
		var Filter	=	ObjElem.data("filter");
		var FX		=	(Filter == undefined)? 'fade' : Filter;
		// this will do an upwards transverse
		var	Instr	=	GetData.split(">");
		// Check for upwards
		if(Instr[1] != undefined) {
				FindData	=	(Instr[0] == 'up')? ObjElem.parents(Instr[1]) : ObjElem.find(Instr[1]);
			}
		else {
				FindData	=	GetData;
			}
		
		console.log(FindData);
		
		if(FX == 'fade')
			$(FindData).fadeOut('fast');
		else
			$(FindData).slideUp('fast');
	}

function CliClose(ObjElem)
	{
		ObjElem.click(function() {
			close_what($(this));
		});
	}

$(document).ready(function(e) {
/********** DOCUMENT WIDTH INDICATOR **********/
	var winSizeCont	=	$("#nbrDocSize");
	window.addEventListener("resize", function() { WindowEngine.dispDocSize(winSizeCont) });
/********** EFFECTS **********/
	$(this).on("click",".ajax_active_click",function() {
		close_what($(this));
	});
	
	// @description: Accordion Effect for tool boxes
	// @required: The panel, needs to be next to the trigger and can only have one class name
	$(this).on("click",".nbrAccordion",function() {
		// Get the class name of the next element
		var GetClass	=	$(this).next().prop("class");
		// Use the object + the next class
		nbrAccordion($(this),$("."+GetClass));
	});
	
//	$(".SubMenuPopUp").on("click",".nbrAccordion",function() {
		// Get the class name of the next element
//		var GetClass	=	$(this).next().prop("class");
		// Use the object + the next class
//		nbrAccordion($(this),$("."+GetClass));
//	});
	
	// @description: Tool Toggler Revealer
	// @required: This gives the page a chance to load before revealing the tool editor
	var ToolToggler	=	$("#nbr_tooltoggle");
	// Shows the toggle menu on load of page
	ToolToggler.delay(1000).animate({ width: "50px" },800);
	ToolToggler.click(function() { 
		nbrSideSlide($('#InspectorPalletWrap'),400);
	});
	// @description: Small closer
	// @required: This allows for a closer class
	CliClose($(".nbr_closer_small"));
	
	$("#ajax_admindrop").on("click",".nbr_reveal",function()	{
		var TogglePanel	=	$(this).data("reveal");
		var UseFX		=	$(this).data("fx");
		var UseWrap		=	$(this).data("wrap");
		UseFX			=	(UseFX == undefined)? 'fade':UseFX;
		UseWrap			=	(UseWrap == undefined)? '.hideall':'.'+UseWrap;
		nbrRevealFX($("#"+TogglePanel),$(UseWrap),UseFX);
	});

/*********** AJAX ***********/

	$(this).on("click",".ajaxtrigger",function() {
		// Assign data attributes
		var DataGoPage	=	$(this).data('gopage');
		var DataSend	=	$(this).data('gopagesend');
		var DataKind	=	$(this).data('gopagekind');
		var	FreezeBkg	=	$(this).data('freeze');
		// Set up defaults
		DataKind		=	(DataKind == undefined)? DetermineAJAXDrop(false) : DetermineAJAXDrop(DataKind);
		FreezeBkg		=	(FreezeBkg == undefined)? false : FreezeBkg;
		
		if(DataSend == undefined)
			DataSend	=	'val=false';
		if(DataGoPage == undefined)
			DataGoPage	=	'core.engine';
		// Assemble call url
		var URL		=	'/core.ajax/'+DataGoPage+'.php?'+DataSend;
		// Send ajax request
		_AJAX(URL,$(DataKind));
		
		if(FreezeBkg) {
			$(FreezeBkg).addClass("no-scroll");
			$("html, body").animate({ scrollTop: 0 }, 'fast');
		}
	});
	
/********** BETA TESTS *************/

	$(".form-input").click(function() {
		var GetInput	=	$(this).find("input");
		var GetInName	=	GetInput.prop("type");
		
		if(GetInName == 'hidden') {
			GetInput.attr("type","text");
		}
	});
	
	$("#tablebutton").click(
		function() {
			if ($("#tablepallet").is(":visible") == false) {
					$("#tablepallet").animate({ width:"300px" });
					$("#tablepallet").fadeIn();
				}
			else {					
					$("#tablepallet").animate({width:"0px",overflow:"hidden"});
					$("#tablepalletcont").css({ "border":"0px"});
					$("#tablepallet").fadeOut();
				}
		});
	
	$(".photo-block").hover(
		function() {
				ScaleUp($(this));
			},
		function() {
				ScaleDown($(this));
			}
		);
	
	// Find all the spans that have trigger assigned to them
	var TrigEffects	=	$("span").hasClass("js_trigger");
	// If there are any, save to an array
	if (TrigEffects) {
			// Default false for loading of function
			var LoadError	=	false;
			// Create our storage array
			var	DataSet		=	[];
			// Create index variable
			var index;
			// Fetch the all trigger instances
			var a			=	$(".js_trigger");
			// Loop through the instances gathered and set to array
			for (index = 0; index < a.length; ++index) {
				DataSet[index]	=	$(a[index]).data("instruct");
			}
			
			// Find key of in array
			var	InstallSet		=	$.inArray('install',DataSet);
			// Find error key
			var	ErrorsSet		=	$.inArray('errors',DataSet);
			// Find load inspector
			var InspectorSet	=	$.inArray('runinspector',DataSet);
			
			// If errors are in the array, fade in the errors and load processing.
			// The value -1 indicates false
			if (InstallSet !== -1) {
					$("#first-start").fadeIn();
					// Ajax call to errors
					if(typeof AjaxFlex == 'function')		
						AjaxFlex("#first-start",'/core.processor/install/install.database.php?run=true');
					else {
							SetFunc		=	'AjaxFlex';
							LoadError	=	true;
						}
				}
			// If there are errors
			if (ErrorsSet !== -1) {
					if(typeof ShowErrors == 'function')
						ShowErrors($(a[ErrorsSet]).text());
					else {
							SetFunc		=	'ShowErrors';
							LoadError	=	true;
						}
				}
			
			if (InspectorSet != -1) {
					$(window).load(function(){
						var SetInspAction	=	$(a[ErrorsSet]).text();
						$('#tooltoggle').click(function() { 
						
									$('#inspectorpaneltoggleWrap').animate({width:'toggle'},200);
									$('#inspectorpanel').animate({width:'toggle'},200);
									
							/*	if(SetInspAction == 'slide') {  
									$('#inspectorpaneltoggleWrap').animate({width:'toggle'},350);
									$('#inspectorpanel').animate({width:'toggle'},350);
								 }
								else {
									$('#inspectorpanel').css({"display":"inline-block"});
									$('#inspectorpaneltoggleWrap').fadeToggle('fast');
								}
							*/
							});
					});
					
			}
			
			// If there are any functions not available, load error message
			if (LoadError == true) {
					var ErrorObj	=	$("#first-start");
					ErrorObj.css({"cursor":"pointer"}).fadeIn('slow').html('<div style="padding: 30px; background-color: red; color: #FFF; text-shadow: 1px 1px 3px #000; text-align: center;"><span style=" font-size: 30px; font-weight: bold;">Load Error: </span><span style=" font-size: 20px;"><i>"'+SetFunc+'"</i> Function unavailable to load action.</span></div>');
					$("#first-start").click(function() {
						$("#first-start").fadeOut("fast");
					});
					
					$(document).keyup(function(e) {
					  if (e.keyCode == 27)
					  	$("#first-start").fadeOut("fast");
					});
				}
				
		}
});