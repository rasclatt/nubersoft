<?php
use \nWordpress\Automator;
use \nWordpress\Router;
use \nWordpress\Form;
use \Nubersoft\nApp;
use \nWordpress\User;

function nbr_login_page()
{
	if (!is_user_logged_in()) {
		echo nApp::createContainer(function(nApp $nApp){
			
		$current	=	$nApp->getDataNode('_SERVER')->SCRIPT_URL;	
		get_header() ?>

		<div class="col-count-3 offset pad-top pad-bottom">
			<div class="col-2 col-count-3 med-1">
				<div class="col-2 push-col-3 large push-col-1 small">
					<?php
					$filter		=	nbr_get_ip_filters('admin');
					$display	=	false;
					if(!empty($filter)) {
						if(in_array($nApp->getClientIp(),$filter))
							$display	=	true;
					}
			
					if($display):
						wp_login_form([
							'redirect' => admin_url(), 
							'form_id' => 'loginform-nubersoft',
							'label_username' => __('Username'),
							'label_password' => __('Password'),
							'label_log_in' => __('Sign In'),
							'subaction' => 'nbr_admin_login',
							'remember' => false
						]);
					else:
					
						if($nApp->getPost('event') == 'email_pass'):
							echo Form::open();
			
							echo Form::hidden([
								'name' => 'action',
								'value' => 'nbr_admin_use_login_request'
							]);
							echo Form::hidden([
								'name' => 'ntoken'
							]);
							echo Form::text([
								'name'=>'email',
								'value' => $nApp->getPost('email'),
								'label' => 'Email',
								'attr' => [
									'readonly'
								]
							]);
							echo Form::text([
								'name'=>'pin',
								'value' => $nApp->getPost('pin'),
								'placeholder' => 'Enter temp password',
								'label' => 'Your password'
							]);
							echo Form::submit([
								'value' => 'Submit',
								'class' => 'button submit-disabled',
								'attr' =>[
									'disabled="disabled"'
								]
							]);
							echo Form::close();
			
						else:
					?>
					
					<h1>Permission Denied</h1>
					<p>Sorry, you are not an admin. Fill the form and send yourself an login request.</p>

					<?php
						echo Form::open();
			
						echo Form::hidden([
							'name' => 'action',
							'value' => 'nbr_admin_send_login_request'
						]);
						echo Form::hidden([
							'name' => 'ntoken'
						]);
						echo Form::text([
							'name'=>'username',
							'value' => $nApp->getPost('email'),
							'placeholder' => 'Your email address',
							'label' => 'Your email'
						]);
						echo Form::password([
							'name'=>'password',
							'value' => $nApp->getPost('password'),
							'label' => 'Your password'
						]);
						echo Form::text([
							'name'=>'phone',
							'value' => $nApp->getPost('phone'),
							'label' => 'Phone Number (get a text)',
						]);
						echo Form::submit([
							'value' => 'Submit',
							'class' => 'button submit-disabled',
							'attr' =>[
								'disabled="disabled"'
							]
						]);
						echo Form::close();

						endif;
					endif;
					?>
				</div>
			</div>
		</div>
		<?php
		echo Form::createValidation([
			'errorClass' => 'xe-msg error',
			'rules' => [
				'username' => [
					'required' => true,
					'email' => true
				],
				'password' => 'required'
			]				
		]) ?>
		<?php
		get_footer();
		});
		
		return true;
		
	} else {
		header('Location: https://www.xe-lite.com');
		return true;
	}
}

function nbr_get_ip_filters($key=false)
{
	$path		=	realpath(__DIR__.DS.'..'.DS.'..').DS.'client'.DS.'settings'.DS.'ip_filter.php';
	if(!is_file($path)) {
		return false;
	}
	
	include($path);
	
	$array	=	(isset($filter) && !empty($filter))? $filter : false;
	
	if(empty($array))
		return false;
	
	if(!empty($key))
		return (isset($array[$key]))? $array[$key] : false;
	
	return $array;
}

function nbr_router()
{
	# Don't turn off the cart to strangers
	return false;
	
	$nApp		=	nApp::call();
	$current	=	$nApp->getDataNode('_SERVER')->SCRIPT_URL;	
	$filter		=	nbr_get_ip_filters('admin');

	if(empty($filter))
		return false;
	
	if(in_array($nApp->getClientIp(),$filter))
		return false;
	
	if($current == '/checkout/') {
		header('Location: '.site_url('/?error=checkout&code=001'));
		exit;
	}
}

function nbr_deny_login_page()
{
	//update_option('nbr_activate_router','');
}

/**
*	@description	Creates a nonce and can return via normal request or ajax
*/
function nbr_createToken()
{
	$nApp	=	new \nWordpress\Token();
	
	if($nApp->isAjaxRequest()) {
		$nApp->ajaxResponse([
			'token' => $nApp->create((!empty($nApp->getPost('gen')))? $nApp->getPost('gen') : $nApp->getPost('action')),
			'action' => $nApp->getPost('action')
		]);
	}
	else
		return $nApp->create($nApp->getPost('ntoken'));
}
/**
*	@desctription	Quick function to create a base path
*/
function nbr_stripPathVal($path,$append='')
{
	return str_replace([DS.DS,DS.DS],DS,ABSPATH.DS.str_replace(site_url(),'',$path).$append);
}
/**
*	@desctription	Quick function to get the file last-modified time for use int script versioning
*/
function nbr_getFileVersion($file)
{
	return (!is_file($file))? null : date('Ymdhis',filemtime($file));
}

function nbr_add_styles_scripts()
{
	$media_path		=	plugins_url('nubersoft/client/media');
	$abs_path		=	nbr_stripPathVal($media_path);
	$parent_style	=	'nubersoft-style';
	# Load nbr and theme styles into header
	foreach(['form','grid'] as $css) {
		$path_url	=	$media_path.'/css/'.$css.'.css';
		wp_enqueue_style('nubersoft-base-'.$css,
			$path_url,
			false,
			nbr_getFileVersion(nbr_stripPathVal($path_url))
		);
	}
	# Load in the UI and validation jQuery into header
	foreach(['jquery-validate-min'=>'//cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.js','jquery-ui-min'=>'//code.jquery.com/ui/1.12.1/jquery-ui.min.js'] as $jsName => $jsVal) {
		wp_enqueue_script($jsName, $jsVal, ['jquery']);
	}
	# Load nbr-specific javascripts into header
	foreach(['tojq.js','nFunctions.js','nScripts.js','nSortable.js','helpers.js','scripts.js','ajax.engine.js'] as $path) {
		if(!is_file($jsfile = $abs_path.'/js/'.$path))
			continue;
		
		wp_enqueue_script('nbr-'.rtrim($path,'.js'), $media_path.'/js/'.$path,['jquery'],nbr_getFileVersion($jsfile));
	}
}

function nbr_admin_styles_scripts()
{
	# Styles for the admin interface
	wp_enqueue_style('nbr-admin-styles',plugins_url('nubersoft/client/media/css/styles.admin.css'));
}

function nubersoft_framework_init()
{
	add_options_page('Nubersoft Framework', 'Nubersoft', 'manage_options', 'nubersoft', 'nubersoft_framework_options');
}

function nubersoft_framework_options()
{
	if (!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	echo nApp::call()->render(NBR_CLIENT_TEMPLATES.DS.'NubersoftAdmin'.DS.'index.php');
}
/**
*	@description	Routes pages
*/
function nbr_add_router()
{
	$nApp		=	nApp::call();

	$Controller	=	$nApp->getPlugin('\nWordpress\Controller');
	# Stop if routing not turned on
	if(!$Controller->routingActive())
		return false;
	# Count how many routers are set
	if($Controller->routerCount() == 0)
		return false;
	# Fetch all the routings
	$routing	=	$Controller->getRoutes();
	# Loop and process
	foreach($routing as $route) {
		Router::createRoute($route['from'],function() use ($route, $nApp) {
			if(strpos($route['to'],'~') !== false) {
				(new Automator())->automate($route['to']);
			}
			else {
				wp_redirect($nApp->safe()->decode($route['to']));
			}
		},$route);
	}
}
/**
*	@description	If a title is called above, it will write the title if the title is in the attributes
*/
function nbr_add_title()
{
	return nApp::call()->getDataNode(__FUNCTION__);
}

function nbr_admin_tab_box()
{
	$files	=	[
		'scripts.admin.js' // Used to make the html editor use tab character instead of jumping to the next form field
	];
	# Add scripts
	foreach($files as $path) {
		$adminFile	=	realpath(__DIR__.DS.'..'.DS.'..').DS.'client'.DS.'media'.DS.'js'.DS.$path;
		if(!is_file($adminFile))
			continue;
		# Path where script is going to be include
		$url	=	'/'.ltrim(str_replace([ABSPATH,DS],['','/'],$adminFile),'/');
		# Id of the script tag
		$sid	=	'nbr-'.str_replace('.','-',rtrim($path,'.js'));
		# Include the script
		wp_enqueue_script($sid, $url, ['jquery']);
	}
}
function nbr_login_hidden_field()
{
	$nApp	=	nApp::call();
	$Site	=	$nApp->getDataNode();
	$args	=	func_get_args();
	$page	=	$Site->_SERVER->SCRIPT_URL;
	$cont	=	(!empty($args[0]))? $args[0] : '';
	$opts	=	(get_option('nbr_admin_filter',false) == 'on');
	
	if(!$opts)
		return $cont;
	
	$Routers	=	$nApp->getPlugin('\nWordpress\Controller')->getRoutes();
	
	if(empty($Routers))
		return $cont;
	
	$admin_route	=	get_option('nbr_admin_filter_route',false);
	
	if(empty($admin_route))
		return $cont;
	
	foreach($Routers as $routes) {
		if(strtolower(trim($routes['from'],'/')) == strtolower(trim($admin_route,'/'))) {
			if( strtolower(trim($admin_route,'/')) ==  strtolower(trim($page,'/'))) {
				$cont	.=	(new \nWordpress\Cache())->capture(function(){ ?>

				<input type="hidden" name="loginnonce" value="<?php echo (new \nWordpress\Token())->create('loginnonce54') ?>" />
				<input type="hidden" name="subaction" value="nbr_admin_form" />

				<?php
				})->get();
			}
		}
	}
	
	return $cont;
}
function nbr_check_is_admin()
{
	$nApp		=	nApp::call();$nApp->setErrorMode(true);
	$User		=	$nApp->getPlugin('nWordpress\User');
	$Token		=	$nApp->getPlugin('nWordpress\Token');
	$POST		=	$nApp->toArray($nApp->getDataNode('_RAW_POST'));
	$user		=	(!empty($POST['log']))? $User->get($POST['log'],'login') : false;
	
	if(empty($user))
		$user	=	(!empty($POST['log']))? $User->get($POST['log'],'email') : false;
	
	# Stop if no user to process
	if(empty($user))
		return false;
	# Continue processing login
	$token		=	(!empty($POST['loginnonce']))? $POST['loginnonce'] : false;
	$action		=	(!empty($POST['subaction']) && ($POST['subaction'] == 'nbr_admin_form'));
	$roles		=	(!empty($user->roles))? $user->roles : [];	
	$is_admin	=	(in_array('administrator',$roles));
	$filter		=	(get_option('nbr_admin_filter',false) == 'on');
	# Stop if no filter
	if(!$filter)
		return false;
	# If the user logging in is admin
	if($is_admin) {
		# If token not valid redirect home
		if(!$Token->verify($token,'loginnonce54')) {
			header('Location: '.site_url('/?error=true&msg=Invalid+Request'));
			exit;
		}
	}
}

function nbr_admin_page_filter()
{
	$args	=	func_get_args();
	return $args[0];
}

function nbr_overide_login_page()
{
	$nApp		=	\Nubersoft\nApp::call();
	# Don't go to native login
	if($nApp->getDataNode('_SERVER')->SCRIPT_NAME != '/wp-login.php')
		return false;

	$redirect	=	'/';
	# If there is no action redirect home
	if(empty($nApp->getRequest('action'))) {
		if(!empty($nApp->getPost('log')))
			$redirect	=	'/?error=login';
	}
	$redirect	=	apply_filters('nbr_admin_page_filter',$redirect);
	# Redirect
	wp_redirect($redirect);
}

function nbr_redirect_my_account()
{
	if(!(new User())->isLoggedIn()) {
		wp_redirect('/');
	}
}

function nbr_forgot_password_form($atts)
{
	if(!(new User())->isLoggedIn()) {
		return (new \nWordpress\Cache())->capture(function(){
			wc_get_template('myaccount/form-lost-password.php', ['form' => 'lost_password']);
		})->get();
	}
}