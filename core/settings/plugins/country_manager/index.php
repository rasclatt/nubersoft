<?php
if(!$this->isAdmin())
	return;
	
$nQuery		=	$this->nQuery();

if($this->getPost('action') == 'nbr_save_locales') {
	$nQuery->query("UPDATE `system_settings` set `content` = :0 where `component` = 'locales' and `name` = 'country'",array(json_encode($this->getPost('country'))));
}
# Get all prefs for this table
$locales	=	$nQuery->query('select `product_sku`,`locale_abbr` from `cart_products_locales`')->getResults();

$list		=	$this->organizeByKey($this->getPlugin('\nPlugins\Nubersoft\ShoppingCart\Model')->getCountryCodes(false,3),'alpha-3');

$current	=	$nQuery->query("select `ID`,`content` from system_settings where `component` = 'locales' AND `name` = 'country'")->getResults(true);

foreach(array_keys($this->organizeByKey($locales,'locale_abbr')) as $Abbr3) {
	$name	=	$list[$Abbr3]['name'];
	if($name == 'Sint Maarten (Dutch part)')
		$name	=	'Saint Maarten';
	elseif($name == 'United Kingdom of Great Britain and Northern Ireland')
		$name	=	'United Kingdom';
	elseif($name == 'United States of America')
		$name	=	'United States';
		
	$filter[$name]	=	$Abbr3;
}

ksort($filter,SORT_NATURAL);

if(!empty($current['content']))
	$countryActive	=	json_decode($this->safe()->decode($current['content']),true);
else
	$countryActive	=	array();
	
# Total countries
$count	=	count($filter);
# Number of rows in column
$cols	= 	ceil($count/4);
?>
<div class="allblocks nbr_ux_element">
	<h1 class="nbr_ux_element">Cart Countries</h1>
	<label style="padding: 10px; border: 1px solid #CCC; cursor: pointer; background-color: #EBEBEB;">
		<input type="checkbox" class="nbr_check_all allOff" data-instructions='{"slave":true,"acton":["#nbr_countries_cart"]}' />&nbsp;<span id="nbr_check_state">Check</span> All
	</label>
	<form method="post" action="" id="nbr_countries_cart">
		<input type="hidden" name="action" value="nbr_save_locales" />
		<input type="hidden" name="ID" value="<?php echo $this->setKeyValue($current,'ID',false) ?>" />
		<table cellpadding="0" cellspacing="0" border="0" class="nbr_standard left p10">
			<tr>
		<?php
		$i = 1;
		
		for($a = 1; $a <= $count; $a++) {
			if($i == 1)
				echo '<td>'; ?>
				<div style="display: inline-block; width: 100%; padding: 5px;">
					<label>
					<input type="checkbox" name="country[<?php echo $couName = key($filter) ?>]" value="<?php echo $couAbbr = $filter[$couName] ?>"<?php if(isset($countryActive[$couName])) echo ' checked' ?> class="nbr_check_all" />&nbsp;<?php echo "{$couName} ({$couAbbr})" ?>
					</label>
				</div>
		<?php 
			if($i == $cols) {
				$i=	0;
				echo '</td>';
			}
			next($filter);
			$i++;
		} ?>
			</tr>
		</table>
		<div class="nbr_button">
			<input type="submit" name="SAVE" value="SAVE" />
		</div>
	</form>
</div>
<script>
$(document).ready(function(e){
	$('.allOff, .allOn').on('click',function(ev) {
		var getCurrent		=	$(this).hasClass('allOff');
		var setCurrState	=	(getCurrent)? 'allOn' : 'allOff';
		$(this).removeClass(((getCurrent)? 'allOff' : 'allOn'));
		$(this).addClass(setCurrState);
		$('.nbr_check_all').prop("checked",(setCurrState == 'allOn'));
		$('#nbr_check_state').text(((setCurrState == 'allOn')? 'Uncheck' : 'Check'));
	});
});
</script>