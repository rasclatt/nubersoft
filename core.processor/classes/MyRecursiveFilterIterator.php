<?php
	class MyRecursiveFilterIterator extends RecursiveFilterIterator
		{
			public $FILTERS	=	array('__MACOSX','.DS_Store','_notes');

			public function accept()
				{
					return !in_array($this->current()->getFilename(), $this->FILTERS, true);
				}
		} ?>