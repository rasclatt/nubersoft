
				<tr>
<?php				foreach($_row as $keys => $values) {
							$echoFormEl	=	$this->verify($keys);
							if($echoFormEl) {
?>
                        <td class="adtoolsTblHD"><?php echo strtoupper(str_replace("_", " ", $keys)); ?></td>
<?php							}
					}
?>
                        <td class="adtoolsTblHD" colspan="2">FUNCTION</td>
				</tr>