function get_wpt(WayPt)
	{
		var Scroll	=	window.pageYOffset;
    	var wayt	=	250;
		
		return (Scroll > WayPt)? true : false;
	}
function CalcDiffScroll(NowSpot,FromOp,ToOp)
	{
		var OpacLimit	=	0;
		var IsGreater	=	false;
				
		if(NowSpot > FromOp) {
				IsGreater		=	true;
				var BaseH		=	(ToOp - FromOp);
				var	NowDif		=	(ToOp - NowSpot);
				var CountDwn	=	(NowDif > 0)? NowDif : 0;
				
				var	UnitVal		=	(100/BaseH)*(0.1);
				var CountUp		=	((100-((UnitVal*NowDif)*10))/100);
				
				var OpacLimit	=	(CountUp >= 1)? 1.0 : CountUp;
				OpacLimit		=	OpacLimit.toString().substr(0,4);
			}
		
		var FinalCalcs	=	{ opacity: OpacLimit, is_greater: IsGreater } 
		
		return FinalCalcs;
	}

function FreezeMenu(NowSpot,StartTrans)
	{
		var Elem		=	$(".toggle_bar_wrap");
		
		if(!Elem)
			return;
		
		var Settings	=	CalcDiffScroll(NowSpot,StartTrans,(+StartTrans + 80));
		
		if(Settings.is_greater) {
			Elem.addClass("toggle_bar_wrap_scroller");
			return true;
		}
		else {
			Elem.removeClass("toggle_bar_wrap_scroller");
			return false;
		}
	}
	
$(window).on('scroll', function() {
	// Get the location of the scroll
	var NowSpot		= window.pageYOffset;
	FreezeMenu(NowSpot,0);
});
