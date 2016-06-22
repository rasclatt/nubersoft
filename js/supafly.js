// JavaScript Document

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
	
	function ScreenPop()
		{
			$("#prefsSlidePanel").fadeIn("fast");
			$("#allHide").fadeOut("fast");
		}
	