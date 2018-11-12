<?php
if($this->isLoggedIn())
	return false;
elseif($this->getPage('is_admin') == 1)
	return false;
elseif(!$this->signUpAllowed())
	return false;

$Token	=	$this->getHelper('nToken');

//$Token->destroy();
if(!$Token->tokenExists('sign_up'))
    $Token->setToken('sign_up');

$Form	=	$this->getHelper('nForm');
echo $Form->open(['action'=>$this->getPage('full_path')]);
echo $Form->fullhide(['name' => 'token[login]', 'value' => '']);
echo $Form->fullhide(['name' => 'action', 'value' => 'sign_up', 'class'=>'nbr']);
?>
	<div class="col-count-3 offset">
		<div class="col-2 col-count-2 gapped" id="admin-login-container">
			<div><?php echo $Form->text(['label' => 'Email Address', 'name' => 'username', 'value' => $this->getPost('username'), 'class'=>'nbr']) ?></div>
			<div><?php echo $Form->password(['label' => 'Password', 'name' => 'password', 'value' => $this->getPost('password'), 'class'=>'nbr']) ?></div>
			<div><?php echo $Form->text(['label' => 'First Name', 'name' => 'first_name', 'value' => $this->getPost('first_name'), 'placeholder' => 'First name', 'class'=>'nbr']) ?></div>
			<div><?php echo $Form->text(['label' => 'Last Name', 'name' => 'last_name', 'value'=> $this->getPost('last_name'), 'placeholder' => 'Last name', 'class'=>'nbr']) ?></div>
			<div><?php echo $Form->submit(['name' => 'sign_up', 'value' => 'Create Account', 'class'=>'nbr button green token_button', 'disabled'=>'disabled', 'other'=> ['data-token="login"']]) ?></div>
		</div>
	</div>
<?php echo $Form->close() ?>