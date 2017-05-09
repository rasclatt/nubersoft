<?php
	if(!defined(NBR_ROOT_DIR))
		include_once('..'.DS.'..'.DS.'..'.DS.'config.php');

use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;
		
	if(is_admin()) :
		autoload_function('nquery,check_empty');
		$nubquery	=	nquery();
		$_table		=	(!empty($_REQUEST['table']))? $_REQUEST['table']:'components';
		$query		=	$nubquery->select(array("ID","unique_id","content"))->from($_table)->where(array("unique_id"=>$_REQUEST['unique_id']))->getResults();
		if($query != 0) {
			$result	=	$query[0]; ?>
    <center>
	<div style="width:auto; display: inline-block; margin: auto; position: relative;">
    	<form action="<?php echo $_SERVER['HTTP_REFERER']; ?>" method="post" enctype="multipart/form-data">
        	<input type="hidden" name="ID" value="<?php if(isset($result['ID']) && !empty($result['ID'])) echo $result['ID']; ?>" />
        	<input type="hidden" name="unique_id" value="<?php if(isset($result['unique_id']) && !empty($result['unique_id'])) echo $result['unique_id']; ?>" />
        	<input type="hidden" name="requestTable" value="<?php echo fetch_table_id('components',$nuber); ?>" />
			<input type="hidden" name="filter_request" value="true" />
			<textarea id="component-wysiwyg" name="content"><?php if(isset($result['content']) && !empty($result['content'])) echo Safe::decode($result['content']); ?></textarea>
            <input type="hidden" name="<?php $function = (isset($result['content']) && !empty($result['content']))? 'update': 'add'; ?>" value="<?php echo $function; ?>" />
            <div class="formButton"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin-top: 20px;" /></div>
        </form>
	</div>
    </center>
<?php
autoload_function("TinyMCE");
echo TinyMCE();
?>
<?php		}
		else { ?>
			<h3>Error: 028111</h3>
            <p>There must be a component already added to use the WYSIWYG editor.</p>
		<?php
		}
	endif; ?>