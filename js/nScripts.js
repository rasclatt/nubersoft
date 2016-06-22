// Create instance of jQuery
var njQuery		=	jQuery;
// Create some "php-like" function
var	n	=
	{
		// Try to mirror empty() - throws undefined error if undefined before hand
		empty: function(value)
			{
				return (value == '' || value == null || value == false || (typeof value === "undefined"));
			}
	};

var nAjax		=	function()
	{
		var	$j				=	njQuery;
		var	useUrl			=	'/core.ajax/ajax.dispatcher.php';
		var	type			=	'post';
		var	error_reporting	=	false;
		var doBefore	;
		var	func;
		
		this.useErrors	=	function(val)
			{
				error_reporting	=	(typeof val !== "undefined")? val : false;
			}
		
		this.useMethod	=	function(val)
			{
				type	=	val;
				return this;
			}
		
		this.setUrl	=	function(URL)
			{
				useUrl	=	URL;
				return this;
			}
		
		this.doBefore	=	function(func)
			{
				doBefore	=	func;
				return this;
			}
		
		this.ajax	=	function(data,func)
			{
				$j.ajax({
					url: useUrl,
					beforeSend: ((typeof doBefore !== "undefined" && typeof doBefore !== null)? doBefore() : function(){ console.log('test'); }),
					type: type,
					data: data,
					success: function(response) {
						if(error_reporting)
							console.log(response);
							
						func(response);
					},
					error: function(response) {
						if(error_reporting)
							console.log(response);
					}
				});
			}
	}

// Create an expire block
var nExpire	=	function()
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
					
				var	cLink		=	(dataObj.on_click != undefined)? dataObj.on_click : '/';
				var	rLink		=	(dataObj.on_reload != undefined)? dataObj.on_reload : '/';
				var	wTime		=	(dataObj.warn_at != undefined)? dataObj.warn_at : 120;
				var	prepenTo	=	(dataObj.append != undefined)? dataObj.append : 'body';
				var	nMessage	=	(dataObj.message != undefined)? dataObj.message : '<div class="nbr_expire_bar">SESSION WILL EXPIRE SOON.</div>';
				var	cNotifier	=	(dataObj.class != undefined)? dataObj.class : '.nbr_expire_bar';
				
				njQuery("body").on('click', cNotifier, function() {
					window.location	=	cLink;
				});
				
				obj	=	setInterval(function() {
					
					if(start <= 0) {
						window.location	=	rLink;
					}
					else if(start == wTime) {
						njQuery("body").prepend(nMessage);
						njQuery(cNotifier).hide().slideDown();
						njQuery(cNotifier).css({"cursor":"pointer"});
					}
					else {
						var strNum	=	start.toString();
					}
					
					start--;
				},1000);
			};	
	};
// Create a smooth scroller applet
var	nScroll	=	function()
	{
		var	scrollClass	=	'.scroll-top';
		var	toggleClass	=	'add-scroll';
		var	startVal	=	100;
		var	useFunction	=	false;
		
		this.init	=	function(data)
			{
				if(data === undefined)
					data		=	{};
				
				scrollClass	=	(data.class !== undefined && !n.empty(data.class))? data.class : scrollClass;
				toggleClass	=	(data.toggle !== undefined && !n.empty(data.toggle))? data.toggle : toggleClass;
				startVal	=	(data.start !== undefined && !n.empty(data.start))? data.start : startVal;
				
				return this;
			};
		
		this.defAnimation	=	function(useFunction)
			{
				useFunction	=	(!n.empty(useFunction))? useFunction : false;
			
				if(n.empty(useFunction)) {
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
					useFunction;
				}
				
				return this;
			};
		
		this.clickScroller	=	function(obj,speed)
			{
				speed	=	(speed !== undefined)? speed : 'slow';
				
				// scroll-to-top animate
				njQuery(obj).click(function (e) {
					njQuery("html, body").animate({ scrollTop: 0 }, speed);
					e.preventDefault();
				});
			};
	};

function is_object(val)
	{
		return (typeof val === "object");
	}

function isset(obj,key)
	{
		if(!is_object(obj))
			return false;
		
		return (typeof obj[key] !== "undefined");
	}

var setActiveObjScope;
var sortActiveObjScope;
var doActionScope;
var doAutomationScope;
var default_actionScope;

njQuery(document).ready(function($j) {
	
	var doc	=	njQuery(this);
	var	activeObj	=	{};
	var nDispatch	=	'/core.ajax/ajax.dispatcher.php';
	var	activeBtn	=	false;
	var setInstr		=	false;
	
	doc.on('click keyup','.nTrigger,.nDom,.nListener,.nKeyUp',function(e) {
		activeBtn	=	$j(this);
		
		if(typeof activeClone === "undefined")
			activeClone	=	activeBtn.clone(true);
			
		setInstr	=	activeBtn.data('instructions');
		if(isset(setInstr,'FX')) {
			if(isset(setInstr.FX,'acton') && isset(setInstr.FX,'fx')) {
				doFx(setInstr.FX.acton,setInstr.FX.fx,activeBtn);
			}
		}
		
		if(isset(setInstr,'DOM')) {
			doDOM(setInstr);
		}
	});
	
	function sortActiveObj(Obj)
		{
			var Sorted		=	{};
			Sorted.target	=	false;	
			Sorted.thisObj	=	false;	
			Sorted.instr		=	(typeof Obj === "object")? Obj : {};
			Sorted.id		=	(isset(Obj,'id'))? Obj.id : false;
			Sorted.class		=	(isset(Obj,'class'))? Obj.class : false;
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
	
	sortActiveObjScope	=	function(Obj)
		{
			return sortActiveObj(Obj);
		};
		
	function setActiveObj(thisBtn,e)
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

	setActiveObjScope	=	function(thisBtn,e)
		{
			return setActiveObj(thisBtn,e);
		};

	function doDOM(obj)
		{
			var getDomInstr	=	obj;
			
			if(isset(getDomInstr.DOM,'sendto')) {
				var getPreg	=	getDomInstr.DOM.sendto;
				var sInstr	=	getPreg.split('/');
				var buildIt	=	activeBtn;
				if(sInstr.length > 1) {
					$.each(sInstr,function(k,v) {
						var getSubAct	=	v.split('::');
						var useV		=	[];
						if(getSubAct.length > 1) {
							useV	=	getSubAct;
						}
						else
							useV	=	useV.push(v);
							
						switch(useV[0]) {
							case('parents'):
								console.log(buildIt);
								buildIt	=	buildIt.parents(useV[1]);
								break;
							case('find'):
								buildIt	=	buildIt.find(useV[1]);
								break;
						}
					});
					
					if(!n.empty(buildIt)) {
						if(!isset(getDomInstr.DOM,'action'))
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
					
					console.log(buildIt);
				}
				else {
					console.log(sInstr);
				}
			}
		}

	function runAjaxObj(obj)
		{
			var	nextDisp		=	(isset(obj,'ajax_disp'))? obj.ajax_disp : nDispatch;
			var	nextFunc		=	(isset(obj,'ajax_func'))? obj.ajax_func : 'default_ajax';
			var	nextDoBefore	=	(isset(obj,'nextDoBefore'))? obj.nextDoBefore : false;
			doAjaxAction(obj,nextFunc,nextDoBefore,nextDisp);
		}

	function default_action(response)
		{
			console.log('CAME BACK');
			try {
				var json	=	JSON.parse(response);
				
				if(isset(json,'fx') && isset(json,'acton')) {
					doFx(json.acton,json.fx);
				}
				
				if(isset(json,'html')) {
					console.log('WROTE TO PAGE DEFAULT');
					if(doHtmlAppend(json.html,activeBtn)){
						writeToPage(json);
					}
				}
				else if(isset(json,'instructions')) {
					var doNowPost	=	sortActiveObj(json.instructions);
					console.log('PARSE INSTRUCTIONS');
					doAutomation(doNowPost,true);
				}
			}
			catch (Exception) {
				console.log(response);
				console.log(Exception.message);
			}
		}

	default_actionScope	=	function(response)
		{
			default_action(response);
		};

	function subFxtor(subfX,thisObj)
		{
			switch (subfX) {
				case('fadeIn'):
					thisObj.fadeIn();
					return true;
				case('fadeOut'):
					thisObj.fadeOut();
					return true;
				case('slideDown'):
					thisObj.slideDown();
					return true;
				case('slideUp'):
					thisObj.slideUp();
					return true;
				case('slideToggle'):
					thisObj.slideToggle();
					return true;
				case('fadeToggle'):
					thisObj.fadeToggle();
					return true;
				case('hide'):
					thisObj.hide();
					return true;
				case('show'):
					thisObj.show();
					return true;
				case('opacity'):
					$j('html').css({"cursor":"progress"});
					thisObj.css({"opacity":"0.5"});
					return true;
				case('rOpacity'):
					$j('html').css({"cursor":"default"});
					thisObj.css({"opacity":"1.0"});
					return true;
				default:
					return false;
			}
		}

	function doFx(actOn,fx,currObj)
		{
			currObj	=	(typeof currObj === "undefined")? false : currObj;

console.log(actOn);
console.log(fx);
			if(!Array.isArray(actOn))
				return false;
			$j.each(actOn,function(k,v){
				if(isset(fx,k)) {
					try {
						var runObj	=	fx[k];
						// Try running default fx
						var runFx	=	subFxtor(runObj,$j(v));
					}
					catch(Exception){
						var	runFx	=	false;
					}
					// If no match to instruction
					if(!runFx) {
						// Try splitting
						// To create this fx use
						// {"data":{"fx":["slide"],"acton":["next::slideToggle"]}}
						var getFxInstr	=	v.split('::');
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
									subFxtor(getFxInstr[1],getObj);
								case('find'):
									getObj	=	currObj.parents('.nParent').find(getFxInstr[1]);
									if(getObj.length == 0)
										return;
									// Try and make fx happen
									subFxtor(getFxInstr[2],getObj);
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
			
			console.log(obj);
			
			if(!useSendTo || !useHtml)
				return false;
			
			
			if(Array.isArray(useSendTo)) {		
				$j.each(useSendTo, function(k,v) {
					if(!Array.isArray(useHtml))
						return false;
					
					if(isset(useHtml,k))
						$j(v).html(useHtml[k]); 
				});
			}
			else {
				if(!Array.isArray(useHtml))
					$j(useSendTo).html(useHtml);
			}
		}
	
	function doAjaxAction(obj,ajaxFunc,doBefore,dispatcher)
		{
			var AjaxEngine	=	new nAjax();
			
			if(typeof dispatcher !== "undefined") {
				AjaxEngine.setUrl(dispatcher);
			}
			
			if(typeof doBefore !== "undefined") {
				AjaxEngine.doBefore(doBefore);
			}
			
			if(typeof ajaxFunc !== "object") {
				switch(ajaxFunc) {
					default:
						AjaxEngine.ajax(obj,function(response){
							console.log('DO AJAX');
							default_action(response);
						});					
				}
			}
			else
				AjaxEngine.ajax(obj,ajaxFunc);
		}
	
	doAjaxActionScope	=	function(obj,ajaxFunc,doBefore,dispatcher)
		{
			doAjaxAction(obj,ajaxFunc,doBefore,dispatcher);
		};
	
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
				if(useInstr.length == 0)
					return true;
				
				$j.each(useInstr,function(k,v){
					if(!isset(packet,'html'))
						return false;
					switch(k) {
						case('append'):
							(v == 'self')? jQObj.append(packet.html) : $j(v).append(packet.html);
							break;
						case('prepend'):
							(v == 'self')? jQObj.prepend(packet.html) : $j(v).prepend(packet.html);
							break;
						case('insertAfter'):
							var UseElem	=	(v == 'self')? jQObj : $j(v);
							if(isset(packet,'remove')) {
								if(packet.remove)
									UseElem.next().remove();
							}
							$j(packet.html).insertAfter(UseElem);
							break;
					}
				});
	
				return true;
			}
			catch(Exception) {
				console.log(Exception.message);
				console.log(packet);
				console.log(jQObj);
			}
			
			return false;
		}
	
	
	function doAutomation(activeObj,burn)
		{
			burn	=	(typeof burn === "undefined")? false : burn;
			
				console.log(activeObj);
			try {
				// Assign actions to do now via Ajax
				var nPacket		=	activeObj.packet;
				var nowAction	=	nPacket.action;
				var nowDispatch	=	nPacket.ajax_disp;
				
				console.log('DO AUTOMATION');
				try	{
					if(isset(nPacket,'html')) {
						console.log('HTML SET');
						if(typeof nPacket.html === "string") {
							console.log('NOT APPEND');
							if(nPacket.html) {
								writeToPage(nPacket);
								console.log('WROTE TO PAGE');
								if(burn) {
									nPacket.html	=	false;
									nPacket.sendto	=	false;
									console.log('BURNT');
								}
							}
						}
						else {
							doHtmlAppend(nPacket.html,activeObj);
						}
					}
				}
				catch(Exception) {
					console.log(Exception.message);
				}
				
				try	{
					if(nowAction) {
						console.log('DO NOW ACTION');
						doAjaxAction(nPacket,'default_action',
							function() {
								if(isset(nPacket,'fx')) {
									if(!n.empty(nPacket.fx))
										doFx(nPacket.acton,nPacket.fx,false);
								}
							},nowDispatch);
					}
				}
				catch(Exception) {
					console.log(Exception.message);
				}
				
				if(nPacket.donext) {
					var doNext	=	nPacket.donext;
					if(isset(doNext,'action')) {
						runAjaxObj(doNext);
					}
				}
				
				try	{
					if(isset(nPacket,'fx')) {
						if(!n.empty(nPacket.fx))
							if(isset(activeObj,'thisObj')) {
								if(!n.empty(activeObj.thisObj))
									doFx(nPacket.acton,nPacket.fx,activeObj.thisObj);
								else
									doFx(nPacket.acton,nPacket.fx,false);
							}
					}
				}
				catch(Exception) {
					console.log(Exception.message);
				}
			}
			catch(Exception) {
				console.log(Exception.message);
			}
		}
	
	doAutomationScope	=	function(activeObj,burn)
		{
			doAutomation(activeObj,burn);
		};
	
	doc.on('click keyup','.nTrigger,.nDom,.nListener,.nKeyUp',function(e) {
		// Get the attribute action
		var aTypeSet	=	e.type;
		// If click and is trigger
		if(aTypeSet == 'click' && activeBtn.hasClass('nTrigger')) {
			// Create the instruction list
			activeObj	=	setActiveObj(activeBtn,e);
			doAutomation(activeObj);
		}
		if(aTypeSet == 'keyup' && activeBtn.hasClass('nKeyUp')) {
			var	thisParent	=	activeBtn.parents('.nKeyUpActOn');
			var	thisData	=	thisParent.data('instructions');
			var thisPacket	=	sortActiveObj(thisData);
			thisPacket.packet.deliver.keyvalue	=	activeBtn.val();
			thisPacket.packet.deliver.keyfield	=	activeBtn.attr('name');
			thisPacket.target					=	activeBtn;
			doAutomation(thisPacket,false);
		}
	});
	
	doc.on('submit','.nbr_ajax_form',function(e) {
		e.preventDefault();
		var thisForm	=	$j(this);
		var sAjax	=	new nAjax();
		var getInstr	=	setActiveObj(thisForm);
		
		getInstr.packet.deliver	=	$j.extend(getInstr.packet.deliver,{formData:thisForm.serialize()});
		
		doAutomation(getInstr,false);
	});
	// Create scrolling
	var	nScroller	=	new nScroll();
	// Create instance
	nScroller.init().defAnimation();
	// Create a clickscroller
	nScroller.clickScroller('.scroll-top');
});