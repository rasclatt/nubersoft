$( ".dragonit" ).draggable({ cancel: ".nondrag"});
$( ".opacitygrab" ).mouseup(function() {
		$( ".dragonit" ).css({"opacity":"1.0"});
		$( ".dragonit" ).css({"box-shadow":"1px 1px 4px rgba(0,0,0,0.4)"});
		$( ".dragonit" ).css({"filter":"blur(0px)"});
		$( ".dragonit" ).css({"-webkit-filter":"blur(0px)"});
		$( ".dragonit" ).css({"cursor":"default"});
	}).mousedown(function() {
		$( ".dragonit" ).css({"opacity":"0.7"});
		$( ".dragonit" ).css({"box-shadow":"0 0 15px  rgba(0,0,0,0.7)"});
		$( ".dragonit" ).css({"filter":"blur(1px)"});
		$( ".dragonit" ).css({"-webkit-filter":"blur(1px)"});
		$( ".dragonit" ).css({"cursor":"move"});
});