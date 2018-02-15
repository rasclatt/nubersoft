<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
namespace nPlugins\Nubersoft\Component;

class Model extends \nPlugins\Nubersoft\CoreTables
{
	public	function getComponent($array,$limitone=true,$node=true,$columns='*',$count=false,$orderby=false)
	{
		return $this->queryComponents($array,$columns,$count,$limitone,$node,$orderby);
	}
	
	public	function queryComponents($array,$columns='*',$count=false,$limit=false,$node=false,$orderby=false)
	{
		foreach($array as $key => $value) {
			$sKey			=	":{$key}";
			$bind[$sKey]	=	$value;
			$new[]			=	"`{$key}` = {$sKey}";
		}
		
		if(is_array($columns))
			$columns	=	implode(',',$columns);
		
		if(is_bool($count) && !empty($count))
			$count	=	'count';
		
		$count	=	(!empty($count))? "COUNT({$columns}) as {$count}" : $columns;
		$where	=	" WHERE ".implode(' AND ',$new);
		$sql	=	"SELECT {$count} FROM components {$where} {$orderby}";
		$query	=	$this->nQuery()->query($sql,$bind);
		
		return ($node)? $query->toNode($limit) : $query->getResults($limit);
	}

	public	function getCodeComponentsFor($unique_id,$columns=false)
	{
		if(empty($columns))
			$columns	=	['ID','component_type', 'content', 'page_live'];
		
		if(is_array($columns))
			$columns	=	implode(',',$columns);
		
		$sql	=	"SELECT
						{$columns}
					FROM
						`components`
					WHERE
						`ref_page` = :0
					ORDER BY
						page_order ASC,
						ID ASC";

		return $this->nQuery()->query($sql,[$unique_id])->getResults();
	}
}