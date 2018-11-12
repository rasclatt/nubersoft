
<div class="col-count-6 gapped lrg-5 med-3 sml-1">
	<?php if(!empty($this->getRequest('create')) || !empty($this->getRequest('edit'))): ?>
	<div><a class="nbr button green small" href="?table=users">Users</a></div>
	<?php endif ?>
	<?php if(empty($this->getRequest('create'))): ?>
	<div><a class="nbr button green small" href="?table=users&create=true">New User</a></div>
	<?php endif ?>
</div>