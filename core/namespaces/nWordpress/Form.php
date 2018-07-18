<?php
namespace nWordpress;

class Form extends \Nubersoft\nApp
{	
	protected	static	$formid;
	
	public	static	function open()
	{
		$args	=	func_get_args();
		$method	=	(!empty($args[0]['method']))? $args[0]['method'] : 'post';
		$class	=	(!empty($args[0]['class']))? $args[0]['class'] : '';
		$id		=	(!empty($args[0]['id']))? $args[0]['id'] : 'my-form';
		$attr	=	(!empty($args[0]['attr']))? implode(' ',$args[0]['attr']) : false;
		
		self::$formid	=	$id;
		
		ob_start();
		?>
		<form method="<?php echo $method ?>" <?php if($class){ ?> class="<?php echo $class ?>"<?php } if($id) { ?> id="<?php echo $id ?>"<?php } if($attr) echo implode(' ',$attr)?>>
		<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
	public	static	function close()
	{
		return '</form>';
	}
	
	public	static	function addSelect()
	{
		$args	=	func_get_args();
		$name	=	(!empty($args[0]['name']))? $args[0]['name'] : false;
		$value	=	(!empty($args[0]['value']))? $args[0]['value'] : false;
		$place	=	(!empty($args[0]['placeholder']))? $args[0]['placeholder'] : false;
		$class	=	(!empty($args[0]['class']))? $args[0]['class'] : false;
		$id		=	(!empty($args[0]['id']))? $args[0]['id'] : false;
		$label	=	(!empty($args[0]['label']))? $args[0]['label'] : false;
		$attr	=	(!empty($args[0]['attr']))? implode(' ',$args[0]['attr']) : false;
		$opts	=	(!empty($args[0]['options']))? $args[0]['options'] : false;
		
		ob_start();
		?>

		<?php if($label): ?>
		<label<?php if($class){ ?> class="<?php echo $class ?>"<?php } ?><?php if($id){ ?> labelfor="<?php echo $id ?>"<?php } ?>><?php echo $label ?></label>
		<?php endif ?>
		<select<?php if($name){ ?> name="<?php echo $name ?>"<?php } ?><?php if($value){ ?> value="<?php echo $value ?>"<?php } ?><?php if($class){ ?> class="<?php echo $class ?>"<?php } ?><?php if($id){ ?> id="<?php echo $id ?>"<?php } ?><?php if($place){ ?>placeholder="<?php echo $place ?>"<?php } ?><?php if($attr) echo $attr ?> />
			<?php
			if(!empty($opts)):
				foreach($opts as $key => $option):
					if(!isset($option['name']) && !isset($option['name'])): ?>
					<option value="<?php echo $key ?>"<?php if(!empty($args[0]['selected']) && $args[0]['selected'] == $key) echo ' selected="selected"'?>><?php echo $option ?></option>
					<?php
					else: ?>
			<option value="<?php echo $option['value'] ?>"<?php if(!empty($option['selected'])) echo ' selected="selected"'?>><?php echo $option['name'] ?></option>
					<?php endif ?>
				<?php endforeach ?>
			<?php endif ?>
			
		</select>
		<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
	
	public	static	function addInput($args)
	{
		$type	=	(!empty($args['type']))? $args['type'] : 'text';
		$name	=	(!empty($args['name']))? $args['name'] : false;
		$value	=	(!empty($args['value']))? $args['value'] : false;
		$place	=	(!empty($args['placeholder']))? $args['placeholder'] : false;
		$class	=	(!empty($args['class']))? $args['class'] : false;
		$id		=	(!empty($args['id']))? $args['id'] : false;
		$label	=	(!empty($args['label']))? $args['label'] : false;
		$attr	=	(!empty($args['attr']))? implode(' ',$args['attr']) : false;
		
		ob_start();
		?>

		<?php if($label): ?>
		<label<?php if($class){ ?> class="<?php echo $class ?>"<?php } ?><?php if($id){ ?> labelfor="<?php echo $id ?>"<?php } ?>><?php echo $label ?></label>
		<?php endif ?>
		<input type="<?php echo $type ?>"<?php if($name){ ?> name="<?php echo $name ?>"<?php } ?><?php if($value){ ?> value="<?php echo $value ?>"<?php } ?><?php if($class){ ?> class="<?php echo $class ?>"<?php } ?><?php if($id){ ?> id="<?php echo $id ?>"<?php } ?><?php if($place){ ?>placeholder="<?php echo $place ?>"<?php } ?><?php if($attr) echo $attr ?> />

		<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
	
	public	static	function __callStatic($name,$args=false)
	{
		$params		=	(!empty($args[0]))? $args[0] : [];	
		$class		=	preg_replace('/^add/','',strtolower($name));
		return self::addInput(array_merge(['type'=>$class],$params));
	}
	
	public	static	function createValidation($args,$jqType='jQuery',$wrap = true)
	{
		ob_start();
		if($wrap)
			echo '<script>';
		?>
		
		<?php echo $jqType ?>(function(){
			
			<?php echo $jqType ?>('#<?php echo self::$formid ?>').validate(
				<?php echo json_encode($args) ?>
			);
		});

		<?php
			
		if($wrap)
			echo '</script>';
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
}