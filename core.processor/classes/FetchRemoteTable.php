<?php
	class	FetchRemoteTable
		{
			protected	static	$_table;
			protected	static	function Connect()
				{
					self::$_table	=	(!isset(self::$_table))? 'all':self::$_table;
					$_url	=	"http://www.nubersoft.com/api/index.php?service=Fetch.Table&table=".urlencode(self::$_table);
					$json	=	new cURL();
					$response	=	$json->Connect($_url);
					return	$response;
				}
			 
			public	static	function Create($nubsql = false,$_table = 'all')
				{
					self::$_table			=	$_table;
					$_tableOpts				=	self::Connect();
					
					if(isset($_tableOpts['error']))
						return false;
					
					$_tableSQL['create']	=	(!empty($_tableOpts['table']))? $_tableOpts['table'] : false;
					$_tableSQL['names']		=	(!empty($_tableOpts['table_name']))? $_tableOpts['table_name'] : false;
					$_tableSQL['rows']		=	(!empty($_tableOpts['rows']))? $_tableOpts['rows'] : false;

					if(!empty($_tableSQL['create']) && isset($nubsql->Write)) {
							foreach($_tableSQL['create'] as $table_id => $statements) {
									
									$nubsql->Write($statements);
									
									$_tbl_name	=	$_tableOpts['table_name'][$table_id];
									
									// Add any columns that need
									$_insert	=	$nubsql->fetch("describe `".$_tbl_name."`");
									
									foreach($_insert as $_fields) {
											$_cols_inTbl[$_tbl_name][]	=	$_fields['Field'];
										}
									
									if(isset($_tableOpts['table_rows'][$table_id])) {
											foreach($_tableOpts['table_rows'][$table_id] as $FieldsId => $ColArray) {
													
													$_ID	=	$FieldsId;
													$_str	=	$_tableOpts['table_rows'][$table_id][$FieldsId]['string'];
													
													if(!in_array($_ID,$_cols_inTbl)) {
															// Add any columns that need
															$nubsql->Write($_str);
														}
													
													if(isset($_tableOpts['rows'][$table_id]['values'])) {
															$_hdRow	=	$_tableOpts['rows'][$table_id]['keys'];
															$_Rows	=	implode(",",$_tableOpts['rows'][$table_id]['values']);
															$nubsql->Write("insert ignore into ".$_tableOpts['table_name'][$table_id]." ($_hdRow) values \r\n $_Rows");
														}
												}
										}
								}
						}
					
					// Send back false (used mainly for check in core file)
					return false;
				}
		}
?>