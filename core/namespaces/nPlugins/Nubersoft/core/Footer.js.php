
					<script>
						<?php if($this->isAdmin()) { ?>
							
                        $('#errorWindow').delay(200).slideDown('slow');
						$('#errorWindow').css({"cursor":"pointer"});
                        $('#errorWindow').click(function() {
								$('#errorWindow').slideUp('fast');
							});
						<?php }
						else { ?>
                        $('#errorWindow').delay(200).slideDown('slow');
                        $('#errorWindow').delay(3000).slideUp('slow');<?php } ?>
						document.getElementById('errorWindow').innerHTML	=	'<?php echo $errorString; ?>';
                    </script>