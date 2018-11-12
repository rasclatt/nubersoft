<?php
$Form	=	$this->getHelper('nForm');
echo $Form->open();
echo $Form->fullhide(['name'=>'action', 'value' => 'contact_webmaster']);
echo $Form->fullhide(['name'=>'token[nProcessor]', 'value' => '']);
echo $Form->text(['name'=>'subject', 'label' => 'Subject', 'value' => $this->getPost('subject'), 'class' => 'nbr', 'other' => ['required']]);
echo $Form->textarea(['name'=>'message', 'value' => $this->getPost('message'), 'label' => 'Message/Question', 'class'=>'nbr medium', 'other' => ['required']]);
echo $Form->submit(['name'=>'send', 'value' => 'SEND', 'class' => 'nbr button green token_button', 'disabled'=>'disabled', 'other'=> ['data-token="nProcessor"']]);
echo $Form->close();