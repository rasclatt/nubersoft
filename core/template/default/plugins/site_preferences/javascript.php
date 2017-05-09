<script>
var Autoload	=	new nAjax($);
var fUploader	=	new nAjax($);

function doQuickPass(response,place)
	{
		
		try {
			echo(response,place);
			var decode	=	JSON.parse(response);
			$(decode.sendto[0]).html(decode.html[0]);
		}
		catch (Exception) {
			echo(Exception.message,'doQuickPass: error');
			echo(response,'doQuickPass: error');
		}
	}

Autoload
	.ajax($('#favIconList').data("instructions"),function(response){
		doQuickPass(response,'icon list');
	})
	.ajax($('#logoList').data("instructions"),function(response){
		doQuickPass(response,'logo list');
	});

$("#uploadFAVICON").on('submit',function(e) {
	e.preventDefault();
	
	echo(this,'favicon upload');
	
	fUploader.formData(this,function(response){
			var imgPath	=	(isset(response,'path'))? response.path : '';
			
			echo(response,'javascript.php');
			
			fUploader.ajax($('#favIconList'));
			
			if(imgPath != '')
				$("#faviconPath").val(imgPath);
		});
});

$("#uploadLogo").on('submit',function(e) {
	e.preventDefault();
	echo(this,'logo upload');
	fUploader.formData(this,function(response){
			var	parseData	=	JSON.parse(response);
			var imgPath		=	(isset(parseData,'path'))? parseData.path : '/client/images/logo/default.png';
			echo(parseData,'formdata');
			fUploader.ajax($('#logoList'),function(response){	
				doQuickPass(parseData,'formdata submit');
			});
			$("#companylogoPath").val(imgPath);
		});
});

$(".nbr_formadd").click(function() {
	var ThisButton	=	$(this);
	var FormName	=	ThisButton.data("formadd");
	var FormGrp		=	ThisButton.data("formgroup");
	
	var	Form		=	$("#"+FormName);
	
	if(FormName == 'settings_site') {	
		Form.append('<div class="new_formelem"><h3>New Element</h3><input type="text" placeholder="Name your new setting." maxlength="20" class="nameit" /><input type="text" name="content[placeholder][]" placeholder="Add a value for this setting." /></div>');
	}
	else if(FormName == 'setting_settings_foot') {
		Form.append('<div class="new_formelem"><h3>New Social Media</h3><input type="text" placeholder="Name your new link (ie. facebook)" maxlength="20" class="nameit" data-formmodel="soc_med" /><input type="text" name="content[placeholder][]" class="sm_url" placeholder="Type in your user URL for this listing" /><input type="text" name="content[placeholder][]" class="sm_img" placeholder="Type in the icon image URL for this listing" /></div>');
	}
});
	
// This is the function to add new settings in the preferences
$(document).on('keyup',".nameit",function(e) {
	
	if(e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 16)
		return false;
		
	var ThisButton	=	$(this);
	var SaveButton	=	ThisButton.next();
	var ThisNewName	=	ThisButton.val().replace(/[^0-9a-zA-Z\_]/g,"").toLowerCase();
	ThisButton.val(ThisNewName);
	var	ThisName;
	if(ThisButton.data("formmodel") != undefined) {
			ThisName	=	"content[social_media]["+ThisNewName+"]";
			SaveButton.prop("name",ThisName+'[url]');
			SaveButton.next().prop("name",ThisName+'[img]');
		}
	else {
			ThisName	=	"content[custom]["+ThisNewName+"]";
			SaveButton.prop("name",ThisName);
		}
	
});

$(".form_removethis").click(function() {
	$(this).parent().find("span").html('<div style="color: red; font-size: 16px;">Save to remove this setting. Reload settings to cancel.</div>');
	$(this).hide();
});

$(".nbr_hidewrap").fadeIn("slow");
$("#loadspot_modal").slideDown("slow");

</script>