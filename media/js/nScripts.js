// Set error reporting
error_reporting		=	true;
// Shows the path the automation takes
path_reporting		=	false;
// Show data packages at key points
package_reporting	=	false;
// Shows the ajax responses
response_reporting	=	true;
// Used to document realtime actions like rollovers
realtime_reporting	=	false;
/*
** @description	This sets up some php-like constants
*/
const	SORT_NATURAL	=	true;
/*
** @description	This sets up the dispatcher
*/
var nDispatch	=	$_SERVER['SCRIPT_URI']+'/index.php';
/*
** @param	Allow custome events to run
** 			Browser Automation JSON:
** 			"nEvents":{"onload_event_before":{"butter":["#222"]},"click":{"butt":["#FFF"]}}
** 			Browser Add Event:
**
**			eEngine.addEvent({
**				'name':'onload_event_before',
**				'use':'butter'
**			},function() {
**				if(arguments[1] != 'click')
**					return false;
**			});
*/
nEventer	=	function()
	{
		var eventObj	=	{};
		var dataObj		=	{};
		/*
		** @description	Adds anonymous functions to populate the object
		** @param	data	This is the name of the event that will be saved
		**					requires {use:whatever,name:whatever}
		** @param	func	This is the anonymous function associated with this object
		*/
		this.addEvent	=	function(data,func)
			{
				data.name	=	(!empty(data.name))? data.name : 'onload_event_before';
				// Don't create event if the space and name are empty
				if(empty(data.use))
					return this;
				// Save to load space
				if(typeof eventObj[data.name] === "undefined") {
					eventObj[data.name]	=	{};
				}
				 
				eventObj[data.name][data.use]	=	func;
				return this;
			}
		/*
		** @description	Runs the event if it exists
		** @param	data	This is the name of the event that will be recalled,
		**					requires {space:whatever,name:whatever}
		** @param	vars	This is what will be passed to the function when run
		** @param	state	This will be the $(document) object
		*/
		this.getEvent	=	function(data,vars,type,obj,state)
			{
				vars	=	(typeof vars === "undefined")? false : vars;
				
				if(isset(eventObj,data.name)) {
					if(isset(eventObj[data.name],data.use))
						eventObj[data.name][data.use](vars,type,obj,state);
				}
			}
		
		this.hasSpace	=	function(loadspace)
			{
				if(empty(loadspace))
					return false;
				
				return (isset(eventObj,loadspace));
			}
		
		this.getSpace	=	function(loadspace)
			{
				if(empty(loadspace))
					return false;
				else if(!isset(eventObj,loadspace))
					return false;
				else if(empty(eventObj[loadspace]))
					return false;
				
				return eventObj[loadspace];
			}
		
		this.getAll	=	function()
			{
				return eventObj;
			}
		
		this.addData	=	function(name,data)
			{
				dataObj[name]	=	data;
				return this;
			}
			
		this.getData	=	function(name)
			{
				return (!empty(dataObj[name]))? dataObj[name] : false;
			}
	}
// Create instance of the self-event engine
eEngine	=	new nEventer();
// Create instance of jQuery
njQuery	=	jQuery;
/*
** PHP-like functions
*/
function Exception(val,code)
	{
		var eMessage	=	(typeof val === "undefined")? "Unknown error occurred" : val;
		var eCode		=	(typeof code === "undefined")? "000" : code;
		this.getMessage	=	function()
			{
				return eMessage;
			}
		this.getErrorCode	=	function()
			{
				return eCode;
			}
	}
/*
**	@description	Runs a simple loader
*/	
function runLoader(getWhileLoad,$)
	{
		if(isset(getWhileLoad,'load_into')) {
			var	loadIntoObj		=	$(getWhileLoad.load_into);
			var getWhileMessage	=	(isset(getWhileLoad,'loader'))? getWhileLoad.loader : 'LOADING...';
			
			if(loadIntoObj.length > 1) {
				$.each(loadIntoObj,function(k,v) {
					
					if(is_array(getWhileMessage))
						$(v).html(getWhileMessage[k]);
					else
						$(v).html(getWhileMessage);
				});
			}
			else
				loadIntoObj.html(getWhileMessage);
		}
	}
/*
**	@description	This will run a custom event based on location event name
*/
function setEventPoint(eventSpace,targetType,obj,doc,$)
	{
		eventSpace	=	(!empty(eventSpace))? eventSpace : 'onload_event_before';
		
		if(!isset(obj,'nEvents'))
			return false;
		else if(!is_object(obj.nEvents))
			return false;
		else if(empty(eEngine.hasSpace(eventSpace)))
			return false;
		
		if(path_reporting) {
			var	repMess	=	'>>>CUSTOM EVENT START: '+eventSpace+'<<<';
			if(targetType == 'mouseout' || targetType == 'mouseover') {
				if(realtime_reporting)
					console.log(repMess);
			}
			else
				console.log(repMess);
		}
		// If there are events in this scope, run them
		$.each(obj.nEvents[eventSpace],function(k,v) {
			eEngine.getEvent({
				'name':eventSpace,
				'use': k
			},v,targetType,obj,doc);
		});
	}
/*
**	@description	This is the main ajax engine
*/
nAjax	=	function($)
	{
		var	useUrl		=	'/index.php';
		var	type		=	'post';
		var doBefore;
		var	func;
		var	dataObj;
		var	thisObj		=	this;
		/*
		**	@description	Allows for passing of data to this object 
		*/
		this.useDataObj	=	function()
			{
				var	getArgs	=	arguments;
				dataObj		=	getArgs[0];
				return this;
			}
		/*
		**	@description	Allows a toggle to switch on error reporting
		*/
		this.useErrors	=	function(val)
			{
				error_reporting	=	(!empty(val))? val : false;
			}
		/*
		**	@description	Allows for sending via $_POST or $_GET
		*/
		this.useMethod	=	function(val)
			{
				if(path_reporting)
					console.log('AJAX SET METHOD');
				type	=	val;
				return this;
			}
		/*
		**	@description	Allows the dispatch url to be diverted to a new one
		*/
		this.setUrl	=	function(URL)
			{
				if(path_reporting)
					console.log('AJAX SET URL');
				
				useUrl	=	URL;
				return this;
			}
		/*
		**	@description	Adds a beforesend anonymouse function that fires on commencment of ajax
		*/
		this.doBefore	=	function(func)
			{
				if(path_reporting)
					console.log('AJAX DO BEFORE-SEND ACTION');
				doBefore	=	func;
				
				return this;
			}
		/*
		**	@description	Runs the ajax
		**	@param	data	[object]	This is the data that will be sent to the dispatch
		**	@param	func	[func]	This is the anonymous function that will run on the return
		*/
		this.ajax	=	function(data,func,isFormData)
			{
				// Create a data object
				var ajaxDataObj		=	{};
				// Create dispatch url
				ajaxDataObj.url		=	useUrl;
				// Save type
				ajaxDataObj.type	=	type;
				// Assign data
				ajaxDataObj.data	=	data;
				// Add a doBefore if set
				if(!empty(doBefore))
					ajaxDataObj.beforeSend	=	doBefore();
				// If this is set as a formData Obj, create prefs
				if(isFormData) {
					ajaxDataObj.processData	=	false;
					ajaxDataObj.contentType	=	false;
				}
				// Create the success function
				ajaxDataObj.success	=	function(response) {
					// Reporting
					if(path_reporting)
						console.log('AJAX OBJ RESPONSE');
					if(response_reporting)
						console.log(response);
					// See if the object is set
					if(!empty(dataObj)) {
						// Pass on the response data
						eEngine.addData('ajax_response_before',response);
						// Run the event point
						setEventPoint('ajax_response_before',false,dataObj,false,$);
					}
					// Run the default function after response
					func(response);
				};
				// Set error function
				ajaxDataObj.error	=	function(response) {
					if(!empty(dataObj)) {
						// Pass on the response data
						eEngine.addData('ajax_response_error',response);
						// Run the event point
						setEventPoint('ajax_response_error',false,dataObj,false,$);
					}
					if(error_reporting) {
						console.log(response);
					}
				};
				// Run the ajax
				$.ajax(ajaxDataObj);
				
				return this;
			}
		
		this.formData	=	function(obj,func)
			{
				try {
					// Save form to formdata
					var formData	=	new FormData(obj);
					// Save form to jQuery obj
					var findForm	=	$(obj);
					// Get the form instructions
					var thisInstr	=	findForm.data('instructions');
					// Find the file input
					var FileInput	=	findForm.find("input[type=file]");
					// Assign the loader dropzone
					var thisLoader	=	$("#nbr_loader");
					// Get the url from the form
					var	ajaxURL		=	useUrl;//(isset(thisInstr,'action'))? thisInstr.action : 'nbr_general_form_data';
					// Get the message(s) if there are any
					var	msgObj		=	findForm.find('input[name=nbr_msg]').val();
					// Parse the messages
					msgVal			=	(!empty(msgObj))? JSON.parse(msgObj) : { success: "Success", fail: "Failed" };
					var uSuccess	=	msgVal.success;
					var uFail		=	msgVal.fail;
					// Set default message
					var defMsg		=	{
						invalid: uFail,
						empty:'File cannot be empty'
					};
					// Assign default response
					var	response	=	{};
					var sendBack;
					this.setResp	=	sendBack;
					var thisDisp	=	this;
					
					thisObj.doBefore(function() {
						if(!empty(thisLoader)) {
							thisLoader.html(thisDisp.doBefore(''));
						}
						
						// Overwrite itself
						FileInput.replaceWith(FileInput.clone(true));
					});
					
					thisObj.ajax(formData,func,true);
				}
				catch (Exception) {
					if(error_reporting)
						console.log(Exception.message);
				}
				
				return this;
			}
	}

// Create an expire block
nExpire	=	function($)
	{
		var start		=	3500;
		var val;
		var dataObj;
		
		this.setStart	=	function(val)
			{
				var is_numeric	=	(!isNaN(parseFloat(val)) && isFinite(val));
				start			=	(is_numeric)? val : start;
				return this;
			};
		
		this.execute	=	function(dataObj)
			{
				if(dataObj === undefined)
					dataObj	=	{};
					
				var	cLink		=	(!empty(dataObj.on_click))? dataObj.on_click : '/';
				var	rLink		=	(!empty(dataObj.on_reload))? dataObj.on_reload : '/';
				var	wTime		=	(!empty(dataObj.warn_at))? dataObj.warn_at : 120;
				var	prepenTo	=	(!empty(dataObj.append))? dataObj.append : 'body';
				var	nMessage	=	(!empty(dataObj.message))? dataObj.message : '<div class="nbr_expire_bar">SESSION WILL EXPIRE SOON.</div>';
				var	cNotifier	=	(!empty(dataObj.class))? dataObj.class : '.nbr_expire_bar';
				
				$("body").on('click', cNotifier, function() {
					window.location	=	cLink;
				});
				
				obj	=	setInterval(function() {
					
					if(start <= 0) {
						window.location	=	rLink;
					}
					else if(start == wTime) {
						$("body").prepend(nMessage);
						$(cNotifier).hide().slideDown();
						$(cNotifier).css({"cursor":"pointer"});
					}
					else {
						var strNum	=	start.toString();
					}
					
					start--;
				},1000);
			};	
	};
// Create a smooth scroller applet
nScroll	=	function()
	{
		var	thisObj		=	this;
		var scrollClass	=	'.scroll-top';
		var toggleClass	=	'add-scroll';
		var startVal	=	100;
		var useFunction	=	false;
		thisObj.useData	=	{};
		
		thisObj.init	=	function(data)
			{
				if(empty(data))
					data	=	{};
				
				thisObj.scrollClass		=	(isset(data,'class') && !empty(data.class))? data.class : scrollClass;
				thisObj.toggleClass		=	(isset(data,'toggle') && !empty(data.toggle))? data.toggle : toggleClass;
				thisObj.startVal		=	(isset(data,'start') && !empty(data.start))? data.start : startVal;
				thisObj.useData	=	(isset(data,'data') && !empty(data.data))? data.data : {};
				
				return this;
			};
		
		thisObj.getData	=	function()
			{
				return thisObj.useData;
			};
			
		thisObj.defAnimation	=	function(useFunction)
			{
				if(empty(useFunction)) {
					// Scroll to top of page.
					njQuery(window).scroll(function () {
						if (njQuery(this).scrollTop() > startVal) {
							njQuery(scrollClass).fadeIn('slow');
							njQuery(scrollClass).addClass(toggleClass);
						}
						else {
							njQuery(scrollClass).css({"display":"none"});
							njQuery(scrollClass).removeClass(toggleClass);
						}
					});
				}
				else {
					useFunction();
				}
				
				return this;
			};
		
		thisObj.clickScroller	=	function(obj,speed)
			{
				speed	=	(speed !== undefined)? speed : 'slow';
				
				// scroll-to-top animate
				njQuery(obj).click(function (e) {
					njQuery("html, body").animate({ scrollTop: 0 }, speed);
					e.preventDefault();
				});
			};
	};

function fetchAllTokens($)
	{
		var nProcIdInput	=	$("input[name=token\\[nProcessor\\]]");
		var nLoginInput		=	$("input[name=token\\[login\\]]");
		var	isActive		=	{
				"login": (nLoginInput.length > 0),
				"nProcessor": (nProcIdInput.length > 0)
			};
		
		if(isActive.login || isActive.nProcessor) {
			// Get the current form
			var	getFormObj	=	nLoginInput.parent('form').children('.token-activate');
			if(!empty(getFormObj)) {
				console.log(getFormObj);
				// Disable the submit button
				getFormObj.prop('disabled',true);
			}
			var nProcId;
			// Create Ajax object
			sAjax	=	new nAjax($);
			sAjax.ajax({
					"action":"nbr_get_form_token",
					"data":{
						"nProcessor": nProcIdInput.val(),
						"login": nLoginInput.val()
					}
				},
				function(response) {
					try {
						var tokenFormData	=	JSON.parse(response);
						// Populate the nProcessor token
						if(isActive.nProcessor) {
							// Fill the value
							nProcIdInput.val(tokenFormData.nProcessor);
							// Get the login form button
							var getLoginButton	=	$('.token_button');
							// See if the token is availble
							var getDataName		=	getLoginButton.data('token');
							// If it's nProcessor, unlock the form
							if(!empty(getDataName) && getDataName == 'nProcessor') {
								getLoginButton.prop('disabled',false);
							}
						}
						
						if(isActive.login) {
							nLoginInput.val(tokenFormData.login);
							getFormObj.prop('disabled',false);
							var getLoginButton	=	$('.token_button');
							var getDataName		=	getLoginButton.data('token');
							if(!empty(getDataName) && getDataName == 'login') {
								getLoginButton.prop('disabled',false);
							}
						}
					}
					catch(Exception) {
						if(error_reporting) {
							console.log(Exception.message);
							console.log(response);
						}
					}
				});
		}
	}
/*
**	@description	Required on an immediate effect. Checks if the event is a click, mouseover, etc
*/
function hasEventList(setInstr,targetType)
	{
		// If there is an event and it has values
		if(!is_array(setInstr.events))
			return false;
		// Assign array
		var hasEvents	=	setInstr.events;
		// See if the target is in the array, if not stop
		if(!in_array(hasEvents,targetType))
			return false;
		
		return true;
	}

	
function sortActiveObj(Obj,nDispatch)
	{
		var Sorted		=	{};
		Sorted.target	=	false;	
		Sorted.thisObj	=	false;	
		Sorted.instr	=	(typeof Obj === "object")? Obj : {};
		Sorted.id		=	(isset(Obj,'id'))? Obj.id : false;
		Sorted.class	=	(isset(Obj,'class'))? Obj.class : false;
		Sorted.packet	=	{
								action:	((isset(Sorted.instr,'action'))? Sorted.instr.action : false),
								sendto:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'sendto'))? Sorted.instr.data.sendto : false),
								html:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'html'))? Sorted.instr.data.html : false),
								donext:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'donext'))? Sorted.instr.data.donext : false),
								deliver:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'deliver'))? Sorted.instr.data.deliver : false),
								fx:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'fx'))? Sorted.instr.data.fx : false),
								acton:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'acton'))? Sorted.instr.data.acton : false),
								ajax_disp:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'ajax_disp'))? Sorted.instr.data.ajax_disp : nDispatch),
								ajax_func:	((isset(Sorted.instr,'data') && isset(Sorted.instr.data,'ajax_func'))? Sorted.instr.data.ajax_func : false)
							}

		return Sorted;
	}

function setActiveObj(thisBtn,e,nDispatch)
	{
		var is_jQuery	=	(thisBtn instanceof jQuery);
		var Obj			=	{};
		var useId		=	(is_jQuery)? thisBtn.attr('id') : false;
		var useClass		=	(is_jQuery)? thisBtn.attr('class') : false;
		Obj.thisObj		=	thisBtn;
		Obj.target		=	(typeof e === "undefined")? false : e.target;
		Obj.instr		=	(is_jQuery)? thisBtn.data('instructions') : false;
		Obj.id			=	(typeof useId !== "undefined")? useId : false;
		Obj.class		=	(typeof useClass !== "undefined")? useClass : false;
		Obj.packet		=	{
								action:	((isset(Obj.instr,'action'))? Obj.instr.action : false),
								sendto:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'sendto'))? Obj.instr.data.sendto : false),
								html:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'html'))? Obj.instr.data.html : false),
								donext:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'donext'))? Obj.instr.data.donext : false),
								deliver:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'deliver'))? Obj.instr.data.deliver : false),
								fx:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'fx'))? Obj.instr.data.fx : false),
								acton:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'acton'))? Obj.instr.data.acton : false),
								ajax_disp:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'ajax_disp'))? Obj.instr.data.ajax_disp : nDispatch),
								ajax_func:	((isset(Obj.instr,'data') && isset(Obj.instr.data,'ajax_func'))? Obj.instr.data.ajax_func : false)
							}

		return Obj;
	}

function doDOM(obj,targetType)
	{
		var getDomInstr	=	obj;
		
		if(isset(getDomInstr.DOM,'sendto')) {
			
			if(isset(getDomInstr.DOM,'html')) {
				if(isset(getDomInstr.DOM,'event')) {
					if(in_array(getDomInstr.DOM['event'],targetType)) {
						writeToPage(getDomInstr.DOM);
					}
				}
			}
			else {
				var getPreg	=	getDomInstr.DOM.sendto;
				var sInstr	=	explode('/',getPreg);
				var buildIt	=	activeBtn;
				if(sInstr.length > 1) {
					$.each(sInstr,function(k,v) {
						var getSubAct	=	explode('::',v);
						var useV		=	[];
						if(getSubAct.length > 1) {
							useV	=	getSubAct;
						}
						else
							useV	=	useV.push(v);
							
						switch(useV[0]) {
							case('parents'):
								if(path_reporting)
									console.log('BUILD IT::PARENTS');
								if(package_reporting)
									console.log(buildIt);
								
								buildIt	=	buildIt.parents(useV[1]);
								break;
							case('find'):
								buildIt	=	buildIt.find(useV[1]);
								break;
						}
					});
					
					if(!empty(buildIt)) {
						if(!isset(getDomInstr.DOM))
							return false;
	
						var	bCont	=	(isset(getDomInstr.DOM,'element'))? getDomInstr.DOM.element : 'value';
						switch(getDomInstr.DOM.action) {
							case('copyvalue'):
								(bCont == 'value')? buildIt.val(activeBtn.val()) : buildIt.val(activeBtn.html());
								break;
							case('copyhtml'):
								(bCont == 'value')? buildIt.val(activeBtn.html()) : buildIt.val(activeBtn.html());
								break;
						}
					}
	
					if(path_reporting) 
						console.log('DO DOM FINISH');
					if(package_reporting)
						console.log(buildIt);
				}
				else {
					if(error_reporting)
						console.log(sInstr);
				}
			}
		}
	}

function runAjaxObj(activeBtn,obj)
	{
		if(path_reporting)
			console.log('AJAX WRAPPER');
			
		var	nextDisp		=	(isset(obj,'ajax_disp'))? obj.ajax_disp : nDispatch;
		var	nextFunc		=	(isset(obj,'ajax_func'))? obj.ajax_func : 'default_ajax';
		var	nextDoBefore	=	(isset(obj,'nextDoBefore'))? obj.nextDoBefore : false;
		
		doAjaxAction(activeBtn,obj,nextFunc,nextDoBefore,nextDisp);
	}

function default_action(activeBtn,response,skipParse)
	{
		//console.log(response);
		// If the response is already an object, skip parsing
		skipParse	=	is_object(response);
		// If the not skipping, check if an json string is present
		if(!skipParse){
			if(empty(preg_match("^\{|\}$",response)))
				return false;
		}
		
		if(path_reporting)
			console.log('***START DEFAULT ACTION***');
		// If there is a possible parsable string or the element is already object, continue
		try {
			var json	=	(skipParse)? response : JSON.parse(response);
			
			if(isset(json,'alert')) {
				alert(json['alert']);
				// Remove alert
				json['alert']	=	null;
			}
			
			// Run page FX
			// Accepts the parent FX or children fx/acton
			if((isset(json,'fx') && isset(json,'acton')) || isset(json,'FX')) {
				// Report
				if(path_reporting)
					console.log('START DEFAULT DO FX');
				// Assign the FX array
				var	getParentFx	=	(isset(json,'FX'))? json.FX : json;
				// Check if there are any speed settings
				var hasSpeed	=	(isset(getParentFx,'fxspeed'))? getParentFx.fxspeed : false;
				// Run the fx engine
				doFx(getParentFx.acton,getParentFx.fx,activeBtn,hasSpeed);
			}
			
			if(isset(json,'html')) {
				if(path_reporting)
					console.log('START DEFAULT WRITE');
				
				if(doHtmlAppend(json.html,activeBtn)){
					if(path_reporting)
						console.log('START DEFAULT WROTE TO PAGE');
					
					writeToPage(json);
				}
			}
			
			if(isset(json,'input')) {
				writeToInput(json);
			}
			// Allows for multi sets of actions to take place
			if(isset(json,'workflow')) {
				if(path_reporting)
					console.log('START WORKFLOW AUTOMATION');
				// Loop through array of workflows
				$.each(json.workflow,function(k,v){
					// Do automation on each workflow
					doAutomation(activeBtn,sortActiveObj(v.instructions,nDispatch));
				});
			}
			// If there is a single workflow, just run here
			if(isset(json,'instructions')) {
				var doNowPost	=	sortActiveObj(json.instructions,nDispatch);

				if(path_reporting) {
					console.log('PARSE INSTRUCTIONS');
					console.log('START DEFAULT AUTOMATION');
				}

				doAutomation(activeBtn,doNowPost,true);
			}
		}
		catch (Exception) {
			if(error_reporting) {
				console.log(response);
				console.log(Exception.message);
			}
		}
		
		if(path_reporting)
			console.log('***WRAP UP DEFAULT ACTION***');
	}

function subFxtor(subfX,thisObj,thisSpeed)
	{
		thisSpeed	=	(typeof thisSpeed == "undefined")? 'slow' : thisSpeed;
		// This will try and get any acton subfx for the object	
		var thisSubFx	=	thisObj.data('subfx');
		var	subFxData	=	false;
				
		switch (subfX) {
			case('fadeIn'):
				if(path_reporting)
					console.log('----> DO SUBFX: FADE IN');
				thisObj.fadeIn(thisSpeed);
				return true;
			case('fadeOut'):
				if(path_reporting)
					console.log('----> DO SUBFX: FADE OUT');
				thisObj.fadeOut(thisSpeed);
				return true;
			case('slideDown'):
				if(path_reporting)
					console.log('----> DO SUBFX: SLIDE DOWN');
				thisObj.slideDown(thisSpeed);
				return true;
			case('slideUp'):
				if(path_reporting)
					console.log('----> DO SUBFX: SLIDE UP');
				thisObj.slideUp(thisSpeed);
				return true;
			case('sideSlide'):
				/*
				**	Requires acton object to supply data for it's effect:
				**	<div id="whatever" data-subfx='{"sideSlide":{"speed":"1000","data":{"width":"toggle"}}}'>
				*/
				if(isset(thisSubFx,'sideSlide')) {
					if(isset(thisSubFx.sideSlide,'speed'))
						thisSpeed	=	thisSubFx.sideSlide.speed;
					
					if(isset(thisSubFx.sideSlide,'data'))
						subFxData	=	thisSubFx.sideSlide.data;
				}
				if(!is_object(subFxData))
					return false;
				thisSpeed	=	(!is_numeric(thisSpeed))? 1000 : thisSpeed;
				thisObj.animate(subFxData,thisSpeed);
				return true;
			case('workflow'):
				if(path_reporting)
					console.log('----> DO SUBFX: CHILD WORKFLOW');
				// Check if there is a matching workflow to run
				if(!isset(thisSubFx,'workflow'))
					return false;
				// Run any CSS events
				if(isset(thisSubFx.workflow,'css')) {
					// See if there are settings for the css method
					if(!isset(thisSubFx.workflow.css,'data'))
						return false;
					// Use jQuery css
					thisObj.css(thisSubFx.workflow.css.data);
				}
				return true;
			case('css'):
				if(path_reporting)
					console.log('----> DO SUBFX: CSS');
				// Check if there is a matching workflow to run
				if(!isset(thisSubFx,'css'))
					return false;
				// See if there are settings for the css method
				if(!isset(thisSubFx.css,'data'))
					return false;
				// Use jQuery css
				thisObj.css(thisSubFx.css.data);
				return true;
			case('slideToggle'):
				if(path_reporting)
					console.log('----> DO SUBFX: SLIDE TOGGLE');
				thisObj.slideToggle(thisSpeed);
				return true;
			case('addClass'):
				if(path_reporting)
					console.log('----> DO SUBFX: ADD CLASS');
				
				if(!isset(thisSubFx,'addClass'))
					return false;
				
				$.each(thisSubFx.addClass.data,function(k,v) {
					console.log(v);
					if(!thisObj.hasClass(v))
						thisObj.addClass(v);
				});
				return true;
			case('removeClass'):
				if(path_reporting)
					console.log('----> DO SUBFX: REMOVE CLASS');
				
				if(!isset(thisSubFx,'removeClass'))
					return false;
				
				$.each(thisSubFx.addClass.data,function(k,v) {
					console.log(v);
					if(thisObj.hasClass(v))
						thisObj.removeClass(v);
				});
				return true;
			case('toggleClass'):
				if(path_reporting)
					console.log('----> DO SUBFX: TOGGLE CLASS');
				
				if(!isset(thisSubFx,'toggleClass'))
					return false;
				
				$.each(thisSubFx.toggleClass.data,function(k,v) {
					console.log(v);
					thisObj.toggleClass(v);
				});
				return true;
			case('accordian'):
				if(path_reporting)
					console.log('----> DO SUBFX: ACCORDIAN');
				if(thisObj.is(":visible"))
					thisObj.slideUp(thisSpeed);
				else
					thisObj.slideDown(thisSpeed);
				
				return true;
			case('toggle'):
				if(path_reporting)
					console.log('----> DO SUBFX: TOGGLE');
				if(thisObj.is(":visible"))
					thisObj.hide();
				else
					thisObj.show();
				return true;
			case('fadeToggle'):
				if(path_reporting)
					console.log('----> DO SUBFX: FADE TOGGLE');
				thisObj.fadeToggle(thisSpeed);
				return true;
			case('hide'):
				if(path_reporting)
					console.log('----> DO SUBFX: HIDE');
				thisObj.hide();
				return true;
			case('show'):
				if(path_reporting)
					console.log('----> DO SUBFX: SHOW');
				thisObj.show();
				return true;
			case('opacity'):
				if(path_reporting)
					console.log('----> DO SUBFX: OPACITY');
				$('html').css({"cursor":"progress"});
				thisObj.css({"opacity":"0.5"});
				return true;
			case('rOpacity'):
				if(path_reporting)
					console.log('----> DO SUBFX: REVERSE OPACITY');
				$('html').css({"cursor":"default"});
				thisObj.css({"opacity":"1.0"});
				return true;
			case('disableToggle'):
				if(path_reporting)
					console.log('----> DO SUBFX: DISABLE TOGGLE');
				var getDisabledProp	=	(thisObj.prop("disabled"))? false : true;
				thisObj.prop("disabled",getDisabledProp);
				return true;
			default:
				if(path_reporting)
					console.log('----> DO SUBFX: NONE');
				return false;
		}
	}

function doFx(actOn,fx,currObj,speed)
	{
		speed			=	(typeof speed !== "undefined")? speed : false;
		currObj			=	(typeof currObj === "undefined")? false : currObj;
		var getObjInstr	=	$(currObj).data('instructions');
		var	hasCancel	=	(isset(getObjInstr,'FX') && isset(getObjInstr.FX,'cancel'))? getObjInstr.FX.cancel : false;
		var eventType	=	((typeof event !== "undefined") && isset(event,'type'))? event.type : false;
			
		if(path_reporting)
			console.log('START DO FX');
		
		if(package_reporting) {
			console.log(speed);
			console.log(actOn);
			console.log(fx);
		}

		if(!Array.isArray(actOn))
			return false;

		$.each(actOn,function(k,v){
			// Check if there is an fx speed associated with matched array
			var setFxSpeed	=	(isset(speed,k))? speed[k] : false;
			// See if there is a cancel event key
			if(isset(hasCancel,k) && (hasCancel[k] == eventType))
				return;
				
			if(isset(fx,k)) {
				try {
					if(path_reporting)
						console.log('--> DO FX');
					
					var runObj		=	fx[k];
					// Try running default fx
					var runFx	=	subFxtor(runObj,$(v),setFxSpeed);
				}
				catch(Exception){
					var	runFx	=	false;
				}
				// If no match to instruction
				if(!runFx) {
					if(path_reporting)
						console.log('--> DO FX SPLIT');
					// Try splitting
					// To create this fx use
					// {"data":{"fx":["slide"],"acton":["next::slideToggle"]}}
					var getFxInstr	=	explode('::',v);
					// If split is good
					if(isset(getFxInstr,0) && isset(getFxInstr,1)) {
						// If there is no object (false)
						if(!currObj)
							return;
						// If there is an object but not instance of jQuery
						else if(!(currObj instanceof jQuery))
							return;
						// Set the object container
						var getObj;
						// loop through what to select
						switch(getFxInstr[0]) {
							case('next'):
								getObj	=	currObj.next();
								if(getObj.length == 0)
									return;
								// Try and make fx happen
								subFxtor(getFxInstr[1],getObj,setFxSpeed);
							case('find'):
								getObj	=	currObj.parents('.nParent').find(getFxInstr[1]);
								if(getObj.length == 0)
									return;
								// Try and make fx happen
								subFxtor(getFxInstr[2],getObj,setFxSpeed);
							default:
								getObj	=	$(getFxInstr[1]);
								if(getObj.length == 0)
									return;
								// Try and make fx happen
								subFxtor(getFxInstr[2],getObj,setFxSpeed);
						}
					}
				}
			}
		});
	}

function writeToPage(obj)
	{
		var	useHtml		=	(isset(obj,'html'))? obj.html : false;
		var	useSendTo	=	(isset(obj,'sendto'))? obj.sendto : false;
		
		if(path_reporting)
			console.log('WRITE TO PAGE START');
		if(package_reporting)
			console.log(obj);
		
		if(!useSendTo || !useHtml)
			return false;
		
		if(Array.isArray(useSendTo)) {		
			$.each(useSendTo, function(k,v) {
				if(!Array.isArray(useHtml))
					return false;
				
				if(isset(useHtml,k)) {
					var getVelem	=	$(v);
					var getVhtml	=	useHtml[k];
					getVelem.html(getVhtml);
				}
			});
		}
		else {
			if(!Array.isArray(useHtml))
				$(useSendTo).html(useHtml);
		}
		
	}

function writeToInput(obj)
	{
		var	useHtml		=	(isset(obj,'input'))? obj.input : false;
		var	useSendTo	=	(isset(obj,'sendto'))? obj.sendto : false;
		
		if(path_reporting)
			console.log('WRITE TO INPUT START');
		if(package_reporting)
			console.log(obj);
		
		if(!useSendTo || !useHtml)
			return false;
		
		if(Array.isArray(useSendTo)) {		
			$.each(useSendTo, function(k,v) {
				if(!Array.isArray(useHtml))
					return false;
				
				if(isset(useHtml,k)) {
					var getVelem	=	$(v);
					var getVhtml	=	useHtml[k];
					getVelem.val(getVhtml);
				}
			});
		}
		else {
			if(!Array.isArray(useHtml))
				$(useSendTo).html(useHtml);
		}
		
	}

function doAjaxAction(activeBtn,obj,ajaxFunc,doBefore,dispatcher)
	{
		if(!empty(dispatcher) || !empty(eEngine.getData('ajax_disp'))) {
			if(path_reporting)
				console.log('START DO AJAX SET URL');
			
			if(!empty(eEngine.getData('ajax_disp')))
				AjaxEngine.setUrl(eEngine.getData('ajax_disp'));
			else
				AjaxEngine.setUrl(dispatcher);
		}
		
		if(typeof doBefore !== "undefined") {
			if(path_reporting)
				console.log('START DO AJAX DO BEFORE');
				
			AjaxEngine.doBefore(doBefore);
		}
		
		if(typeof ajaxFunc !== "object") {
			switch(ajaxFunc) {
				default:
					AjaxEngine.ajax(obj,function(response){
						if(path_reporting)
							console.log('AJAX RETURN: ACT ON ON RESPONSE');

						default_action(activeBtn,response);
					});					
			}
		}
		else {
			if(path_reporting)
				console.log('START DO AJAX NOT OBJECT');
		
			AjaxEngine.ajax(obj,ajaxFunc);
		}
	}

function doHtmlAppend(packet,jQObj)
	{
		try{
			var useInstr	=	{};
			if(isset(packet,'append'))
				useInstr.append	=	packet.append;
			else if(isset(packet,'prepend'))
				useInstr.prepend	=	packet.prepend;
			else if(isset(packet,'insertAfter'))
				useInstr.insertAfter	=	packet.insertAfter;
			
			if(empty(useInstr.length)) {
				
				if(path_reporting)
					console.log('APPEND STOPPED');
				if(package_reporting)
					console.log(jQObj);
			
				
				if(isset(jQObj,'packet')) {
					if(isset(jQObj.packet,'sendto') && isset(jQObj.packet,'sendto')) {
						$.each(jQObj.packet.sendto,function(k,v){
							$(v).html(jQObj.packet.html[k]);
						});
					}
				}
				
				return true;
			}
			
			if(path_reporting) {
				console.log(useInstr.length);
			}
			
			$.each(useInstr,function(k,v){
				if(!isset(packet,'html'))
					return false;
				switch(k) {
					case('append'):
						(v == 'self')? jQObj.append(packet.html) : $(v).append(packet.html);
						break;
					case('prepend'):
						(v == 'self')? jQObj.prepend(packet.html) : $(v).prepend(packet.html);
						break;
					case('insertAfter'):
						var UseElem	=	(v == 'self')? jQObj : $(v);
						if(isset(packet,'remove')) {
							if(packet.remove)
								UseElem.next().remove();
						}
						$(packet.html).insertAfter(UseElem);
						break;
				}
			});

			return true;
		}
		catch(Exception) {
			if(error_reporting) {
				console.log(Exception.message);
				console.log(packet);
				console.log(jQObj);
			}
		}
		
		return false;
	}


function doAutomation(activeBtn,activeObj,burn)
	{
		burn	=	(typeof burn === "undefined")? false : burn;

		if(path_reporting)
			console.log('***START AUTOMATION***');
		if(package_reporting)
			console.log(activeObj);
		
		try {
			// Assign actions to do now via Ajax
			var nPacket		=	activeObj.packet;
			var nowAction	=	nPacket.action;
			var nowDispatch	=	nPacket.ajax_disp;

			if(path_reporting)
				console.log('DO AUTOMATION');
			
			if(package_reporting)
				console.log(activeObj);

			try	{
				
				if(isset(nPacket,'input')) {
					writeToInput(nPacket);
				}
				
				if(isset(nPacket,'html')) {
					if(path_reporting)
						console.log('HTML SET');

					if(typeof nPacket.html === "string") {
						if(path_reporting)
							console.log('NOT APPEND');

						if(nPacket.html) {
							writeToPage(nPacket);

							if(path_reporting)
								console.log('WROTE TO PAGE');

							if(burn) {
								nPacket.html	=	false;
								nPacket.sendto	=	false;

								if(path_reporting)
									console.log('BURNT');
							}
						}
					}
					else {
						if(path_reporting)
							console.log('DO APPEND');
						
						doHtmlAppend(nPacket.html,activeObj);
					}
				}
			}
			catch(Exception) {
				if(error_reporting)
					console.log(Exception.message);
			}
			
			if(path_reporting)
				console.log('NOW ACTION: '+nowAction);
			if(package_reporting)
				console.log(nPacket);
		
			try	{
				if(nowAction) {
					if(path_reporting)
						console.log('START NOW AJAX');
					
					// Run the AJAX event
					doAjaxAction(activeBtn,nPacket,'default_action',
						function() {
							if(isset(nPacket,'fx')) {
								if(!empty(nPacket.fx)) {
									var nPSpeed	=	(isset(nPacket,'fxspeed'))? nPacket.fxspeed : false;
									doFx(nPacket.acton,nPacket.fx,false,nPSpeed);
								}
							}
						},nowDispatch);
				}
			}
			catch(Exception) {
				if(error_reporting)
					console.log(Exception.message);
			}
			
			if(nPacket.donext) {
				if(path_reporting) {
					console.log('START DO NEXT');
				}
				var doNext	=	nPacket.donext;
				if(isset(doNext,'action')) {
					runAjaxObj(activeBtn,doNext);
				}
			}
			
			try	{
				if(isset(nPacket,'fx')) {
					if(path_reporting)
						console.log('START FX AUTOMATION PACKET');
						
					if(package_reporting)
						console.log(nPacket);
					
					if(!empty(nPacket.fx)){
						var	doDefaultFx	=	true;
						if(isset(activeObj,'thisObj')) {
							if(!empty(activeObj.thisObj)) {
								nPSpeed		=	(isset(nPacket,'fxspeed'))? nPacket.fxspeed : false;
								doDefaultFx	=	false;
								doFx(nPacket.acton,nPacket.fx,activeObj.thisObj,nPSpeed);
							}
						}
						if(doDefaultFx) {
							if(path_reporting)
								console.log('START FX PACKET');
								
							doFx(nPacket.acton,nPacket.fx,false);
						}
					}
				}
			}
			catch(Exception) {
				if(error_reporting)
					console.log(Exception.message);
			}
		}
		catch(Exception) {
			if(error_reporting)
				console.log(Exception.message);
		}
		
		if(path_reporting)
			console.log('***WRAP UP AUTOMATION***');
	}

function doEventAction(activeBtn,nDispatch)
	{
		if(path_reporting) {
			console.log('START EVENT');
		}
		// Searches for the parent wrapper that contains instructions
		var	thisParent	=	activeBtn.parents('.nKeyUpActOn');
		
		if(empty(thisParent)) {
			thisParent	=	activeBtn.parents('.nForm');
		}
		// Get the instructions
		var	thisData	=	thisParent.data('instructions');
		// Process the instructions
		var thisPacket	=	sortActiveObj(thisData,nDispatch);
		
		if(package_reporting) {
			console.log(thisParent);
			console.log(thisData);
			console.log(thisPacket);
		}
		
		runLoader(thisData,jQuery);
		
		if(get_dom_type(thisParent) == 'FORM') {
			var	serializedData	=	thisParent.serialize();
			// !****** DUPLICATE OF SUBMIT **********! //
			// Combine data
			// Create a delivery package of the typing values
			thisPacket.packet.deliver	=	{ 
				form: serializedData
			};
			// Set the target as the current acting object
			thisPacket.target	=	activeBtn;
			
			console.log(thisPacket);
			
			// Run the automation
			doAutomation(activeBtn,thisPacket,false);
		}
		else {
			// Create a delivery package of the typing values
			thisPacket.packet.deliver	=	{ 
				keyvalue : activeBtn.val(),
				keyfield : activeBtn.attr('name')
			};
			// Set the target as the current acting object
			thisPacket.target	=	activeBtn;
			// Run the automation
			doAutomation(activeBtn,thisPacket,false);
		}
	}

function runWorkflow_FX(activeBtn,targetType,setInstr,doc,$)
	{
		if(isset(setInstr,'FX')) {
			// Checks if the item has an event
			if(isset(setInstr,'events')) {
				if(!hasEventList(setInstr,targetType))
					return false;
			}
			else if((targetType == 'mouseout' || targetType == 'mouseover') && !activeBtn.hasClass('nRollOver'))
				return false;
			
			// Show point locator
			if(path_reporting)
				console.log('START NOW FX','runWorkflow_FX');
			// Run event here
			setEventPoint('onload_fx_before',targetType,setInstr,doc,$);
			// Run the fx if there is an acton array
			if(isset(setInstr.FX,'acton') && isset(setInstr.FX,'fx')) { 
				var thisFxData	=	setInstr.FX;
				var thisFxSpeed	=	(isset(thisFxData,'fxspeed'))? thisFxData.fxspeed : false;
				doFx(thisFxData.acton,thisFxData.fx,activeBtn,thisFxSpeed);
				// Run success fx event
				setEventPoint('onload_fx_success',targetType,setInstr,doc,$);
			}
			// Run after fx event
			setEventPoint('onload_fx_after',targetType,setInstr,doc,$);
		}
	}

function runWorkflow_DOM(targetType,setInstr,doc,$)
	{
		if(isset(setInstr,'DOM')) {
			if(path_reporting)
				console.log('START NOW DOM','runWorkflow_DOM');
			// Run a before event
			setEventPoint('onload_dom_before',targetType,setInstr,doc,$);
			doDOM(setInstr,targetType);
			// Run an after event
			setEventPoint('onload_dom_after',targetType,setInstr,doc,$);
		}
	}

function runWorkflow_HTML(activeBtn,targetType,setInstr,skip)
	{
		//skip	=	(empty(skip))? false : true;
		if(isset(setInstr,'html')) {
			if(!hasEventList(setInstr,targetType))
				return false;
			if(package_reporting)
				console.log('######### WORKFLOW::HTML ########');
			default_action(activeBtn,setInstr);
		}
	}

function runWorkflow_DEFAULT(activeBtn,setInstr,skip)
	{
		skip	=	(empty(skip))? false : true;
		default_action(activeBtn,setInstr,skip);
	}

var setActiveObjScope;
var sortActiveObjScope;
var doActionScope;
var doAutomationScope;
var default_actionScope;
// Create global ajax object
var	AjaxEngine	=	new nAjax(njQuery);
// When the document is ready
njQuery(document).ready(function($) {
	var	currClick;
	var doc			=	$(this);
	var	activeObj	=	{};
	var	activeBtn	=	false;
	var setInstr	=	false;
	var	hasListener	=	$('.nListener');
	// Runs any on-load events
	if(!empty(hasListener)) {
		$.each(hasListener, function(k,v) {
			setInstr	=	$(v).data('instructions');
			AjaxEngine.useDataObj(setInstr);
			doAutomation(activeBtn,sortActiveObj(setInstr,nDispatch),true);
		});
	}
	// When event happens
	doc.on('click keyup change mouseover mouseout','.nTrigger,.nDom,.nListener,.nKeyUp,.nChange,.nRollOver',function(e) {
		// Saves the event type (click,mouseout,mouseover,etc.)
		var	targetType	=	e.type;
		// This is a reporting setting. true will show the wrap up message if proper reporting on
		var wrapReport	=	true;
		// Check if target is annoying type
		var mouseAction	=	(targetType == 'mouseover' || targetType == 'mouseout');
		if(targetType == 'click')
			currClick	=	$(this);
		// If reporting is turned on, show
		if(path_reporting) {
			var pathErr	=	'***START ACTION LEVEL 1: '+e.type+'***';
			if(mouseAction) {
				if(realtime_reporting)
					console.log(pathErr);
				else
					wrapReport	=	false;
			}
			else
				console.log(pathErr);
		}
		// Assign the current active button object
		activeBtn	=	$(this);
		// Clone the current object
		//!!-- SHOULD BE CONSIDERED ON WHEN AND HOW --!!
		if(typeof activeClone === "undefined") {
			if(path_reporting) {
				console.log('CLONE OBJECT','doc.on');
			}
			// Clone current element
			activeClone	=	activeBtn.clone(true);
		}
		// Parse instructions from the target
		setInstr	=	activeBtn.data('instructions');
		// Save the current instance to data for Ajax
		AjaxEngine.useDataObj(setInstr);
		// Create an event action here
		setEventPoint('onload_event_before',targetType,setInstr,doc,$);
		// If there is an FX, run that
		runWorkflow_FX(activeBtn,targetType,setInstr,doc,$);
		// If there is a DOM event, run that
		runWorkflow_DOM(targetType,setInstr,doc,$);
		// Do an automation
		runWorkflow_HTML(activeBtn,targetType,setInstr);
		// If there is a rollover element
		if(activeBtn.hasClass('nRollOver')) {
			// If there are notes
			if(isset(setInstr,'note')) {
				// Clone the current element
				currentRollElem	=	$(activeBtn).clone();
				// If mouse over
				if(e.type == 'mouseover') {
					if(path_reporting) {
						if(realtime_reporting)
							console.log('SHOW NOTE','doc.on');
					}
					var hasDivClass	=	(isset(setInstr,'note_class'))? ' class="'+setInstr.note_class+'"' : '';
					// Create inner element from item
					cloneRollElem	=	activeBtn.html()+'<div'+hasDivClass+'>'+setInstr.note+'</div>';
					// Replace
					$(activeBtn).html(cloneRollElem);
					// Assign old for mouse out
					lastCloneElem	=	currentRollElem;
				}
				else {
					if(path_reporting) {
						if(realtime_reporting)
							console.log('HIDE NOTE','doc.on');
					}
					// Reset the element
					$(activeBtn).replaceWith(lastCloneElem);
				}
			}
		}
		/*
		**	@description	Create a click event
		*/
		setEventPoint('onload_event_after',targetType,setInstr,doc,$);
		
		if(path_reporting && wrapReport)
			console.log('***WRAP UP: '+e.type+'***','doc.on');
	});
	
	doc.on('click keyup change','.nTrigger,.nDom,.nListener,.nKeyUp,.nChange',function(e) {
		// Get the attribute action
		var aTypeSet	=	e.type;
		// If click and is trigger
		if(aTypeSet == 'click' && activeBtn.hasClass('nTrigger')) {
			// Run the loader
			runLoader(setInstr,$);
			// Create the instruction list
			activeObj	=	setActiveObj(activeBtn,e,nDispatch);
			
			if(path_reporting)
				console.log('***START AUTOMATION LEVEL 1***');
			if(package_reporting)
				console.log(activeObj);
				
			// If the button contains a copy mechanism, run that
			if(isset(activeObj.instr,'copy_text')) {
				// Loop through the container array
				$.each(activeObj.instr.copy_text,function(k,v) {
					// Get the object from the value
					var	writeToObj	=	$(v);
	
					if(path_reporting)
						console.log('AUTOMATION COPY TEXT');
					if(package_reporting)
						console.log(activeObj);
						
					// If the key value is a number (array), the copy the text to a text 
					if(typeof k === "number")
						writeToObj.text(activeObj.thisObj.text());
					// If the key value is a string (object), then copy the text to a value (form input)
					else
						writeToObj.val(activeObj.thisObj.text());
				});
			}
			else if(isset(activeObj.instr,'copy_value')) {
				
			}
		
			if(!isset(activeObj.instr,'noauto')) {
				if(path_reporting)
					console.log('CLICK AUTOMATION');
				
				doAutomation(activeBtn,activeObj);
			}
		}
		
		if(aTypeSet == 'change' && activeBtn.hasClass('nChange')) {
			if(path_reporting) {
				console.log('START CHANGE ACTION');
			}
			runLoader(setInstr,$);
			doEventAction(activeBtn,nDispatch);
		}
		
		if(aTypeSet == 'keyup' && activeBtn.hasClass('nKeyUp')) {
			if(path_reporting) {
				console.log('START KEYUP ACTION');
			}
			
			if(package_reporting) {
				console.log(setInstr);
			}
			
			runLoader(setInstr,$);
			doEventAction(activeBtn,nDispatch);
		}
		
		if(path_reporting)
			console.log('***WRAP UP: '+aTypeSet+'***');
	});

	doc.on('submit','.nbr_ajax_form',function(e) {
		if(path_reporting) {
			console.log('***START FORM AJAX***');
		}
		e.preventDefault();
		var thisForm	=	$(this);
		var sAjax		=	AjaxEngine;
		var getInstr	=	setActiveObj(thisForm,e,nDispatch);
		// Combine data
		getInstr.packet.deliver	=	$.extend(getInstr.packet.deliver,{
			formData:thisForm.serialize()+'&click_action='+((is_object(currClick))? currClick.val() : 'NULL')
		});
		// Save instructions to data
		AjaxEngine.useDataObj(getInstr);
		// Run the automator
		doAutomation(activeBtn,getInstr,false);
		
		if(path_reporting)
			console.log('***WRAP UP: '+e.type+'***');
	});
	// Create scrolling
	var	nScroller	=	new nScroll();
	// Create instance
	nScroller.init().defAnimation();
	// Create a clickscroller
	nScroller.clickScroller('.scroll-top');
	
	/*
	**	Get's tokens for the login form
	*/
	
	fetchAllTokens($);
	
	$('.dragonit').draggable({"cancel":".nodrag"});
	var	getDisabled	=	$('.disabled-submit');
	if(!empty(getDisabled)) {
		$.each(getDisabled,function(k,v) {
			if(!$(v).hasClass('token_button'))
				$(v).attr('disabled',false);
		});
	}
	// Cancel the loadspot modal
	$(this).on("keyup",function(e) {
		// First check is clicked
		if(e.keyCode == 27) {
			// Automatically reverse body
			subFxtor('rOpacity',$('body'));
			// Get the value
			var getModal	=	$("#loadspot_modal");
			// If it has content overwrite it
			if(!empty(getModal.html())) {
				getModal.fadeOut();
				//getModal.html('');
			}
		}
	});
});