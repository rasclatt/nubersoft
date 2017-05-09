
<script>
$(document).ready(function() 
	{
		$("#theForm").ajaxForm({url: 'server.php', type: 'post'})
		
		$("#install_button").click(function()
			{
				$("#installer").fadeToggle("fast");
				$("#quick_builder").fadeToggle("fast");
			});
	});
</script>
<style type="text/css">

</style>