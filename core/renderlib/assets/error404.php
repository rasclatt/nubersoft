
            <?php if(!isset($message)) exit; ?>
            <table style="width: 100%; height: 100%;">
            	<tr>
                	<td style="text-align: center; vertical-align: middle;width: 100%;">
                    	<div style="text-align: left; display: inline-block; width: 500px; height: 300px; padding: 30px;">
                            <h2><?php echo $message['title']; ?></h2>
                            <p><?php echo $message['body']; ?></p>
                            <?php global $_incidental;
								if(isset($_incidental['database']['con']))
									include_once(NBR_RENDER_LIB.DS.'admintools'.DS.'plugins'.DS.'site.login.php'); ?>
                    	</div>
                    </td>
                </tr>
            </table>