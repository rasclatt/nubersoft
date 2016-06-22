<?php if(!isset($access)) exit; ?>
<style>
.search_wrapper {
	display: inline-block;
	width: 100%;
	float: left;
	clear: none;
	text-align: left;
	margin: 30px 0;
}
.search_container {
	display: inline-block;
	width: 430px;
}
.search_bar_set input {
	display: inline-block;
	min-height: 30px;
	font-size: 15px;
	text-align: left;
	color: #666666;
	padding: 0 5px;
	border: 1px solid #666666;
	border-bottom-left-radius: 6px;
	border-top-left-radius: 6px;
	margin: 0;
	float: left;
	clear: left;
}
.search_bar_search input {
	display: inline-block;
	background-image: url(/admintools/images/search_big.png);
	background-repeat: no-repeat;
	height: 32px;
	width: 32px;
	border: 1px solid;
	border-radius: 6px;
	background-color: #333333;
	margin: 0;
	background-position: center;
	background-size: 80%;
	float: left;
	clear: none;
	cursor: pointer;
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
}
div.search_container {
	width: auto;
	display: inline-block;
	padding: 5px;
	margin: 0 auto;
}
div.search_bar_search input {
	display: inline-block;
	float: left;
}
div.search_bar_set input {
	display: inline-block;
	float: left;
}
</style>