<?php
namespace Nubersoft;

class nText
	{
		/*
		**	@param	$lib		Saves the pspell_new() resource
		**	@param	$addWords	This will be a list of words to add to a skip list
		**	@param	$case		This will determine which method to skip. false is case-incensitive
		*/
		private	$lib,
				$addWords,
				$case,
				$word,
				$personal;
		/*
		**	This is the default for dictionary link
		*/
		const	ENGLISH	=	'en';
		/*
		**	@param	$singleton	[obj]	This value will store the instance of this object for reuse 
		*/
		private	static	$singleton;
		/*
		**	@description	This returns itself
		**	@param	$lib	[str]	This value is the library the function will use to check words against
		**							The default is english 'en'
		*/
		public	function __construct($lib = false)
			{
				try {			
					// Check if the function is even available
					// Throw error if not
					if(!function_exists('pspell_new'))
						throw new \Exception($this->errorMsg('1001')); 
						
					if(!empty($lib))
						$this->useDictionary($lib);
						
					if(self::$singleton instanceof \Nubersoft\nText)
						return self::$singleton;
					
					self::$singleton	=	$this;
				}
				catch (\Exception $e) {
					die($e->getMessage());
				}
			}
		/*
		**	@description	This method will assign the library to the function (see construct)
		*/
		public	function useDictionary($lib = 'en')
			{
				// Create resource
				$this->lib	=	@pspell_new($lib);
				if(empty($this->lib))
					throw new \Exception($this->errorMsg('1002',$lib));
				// Return for chaining
				return $this;
			}
		/*
		**	@description	This method will assign the library to the function using personal settings
		**	@resource		http://php.net/manual/en/function.pspell-new-personal.php
		*/
		public	function usePersonal($settings = array('pws'=>false,'lib'=>'en','spelling'=>'','jargon'=>'','encode'=>'','opts'=>false))
			{
				$pws_file	=	(!empty($settings['pws']))? $settings['pws'] : false;
				$lib		=	(!empty($settings['lib']))? $settings['lib'] : 'en';
				$spelling	=	(!empty($settings['spelling']))? $settings['spelling'] : '';
				$jargon		=	(!empty($settings['jargon']))? $settings['jargon'] : '';
				$encode		=	(!empty($settings['encode']))? $settings['encode'] : '';
				$opts		=	(!empty($settings['opts']))? $settings['opts'] : PSPELL_FAST|PSPELL_RUN_TOGETHER;
				// Save for updates
				$this->personal	=	$pws_file;
				// Create resource
				$this->lib	=	@pspell_new_personal($pws_file,$lib,$spelling,$jargon,$encode,$opts);
				if(empty($this->lib))
					throw new \Exception($this->errorMsg('1003',$settings));
				// Return for chaining
				return $this;
			}
		/*
		**	@description	This method will create a custom configuration based on saved settings native to the
		**					pspell_* function library. This takes advantage of the ability to add custom words
		**					using the native function library. Use addWords() method when using an array to
		**					check against.
		*/
		public	function createLib($settings = false)
			{
				$lib	=	(!empty($settings['lib']))? $settings['lib'] : 'en';
				$repl	=	(!empty($settings['replacements']))? $settings['replacements'] : false;
				$pers	=	(!empty($settings['personal']))? $settings['lib'] : 'personal';
				// Create a custom configuration
				$custom	=	@pspell_config_create($lib);
				// Throw error if library wrong
				if(empty($custom))
					throw new \Exception($this->errorMsg('1003',$settings));
				// Use personal words
				if($pers)
					pspell_config_personal($custom, $pers);
				else
					throw new \Exception($this->errorMsg('1004',array('personal list','http://php.net/manual/en/function.pspell-new-personal.php')));
				// Use replacement pairs
				if($repl)
					pspell_config_repl($custom, $repl);
				else
					throw new \Exception($this->errorMsg('1004',array('replacement pairs list','http://php.net/manual/en/function.pspell-new-personal.php')));
				// New resource
				return pspell_new_config($custom);
			}
		/*
		**	@description	This method will use a custom configuration based on saved settings native to the
		**					pspell_* function library.
		*/
		public	function customConfig($settings = array('lib'=>'en','personal'=>false,'replacements'=>false))
			{
				try {
					// Check if the function is even available
					// Throw error if not
					// Store resource
					$this->lib	=	$this->createLib($settings);
					// Return for chaining
					return $this;
				}
				catch(\Exception $e) {
					// Die with notification
					die($e->getMessage());
				}
			}
		
		public	function storeWordRelation($misspelled,$correct)
			{
				pspell_store_replacement($this->lib, $misspelled, $correct);
				$this->saveWordList();
			}
		/*
		**	@description	This method checks a word for against the dictionary
		**	@param	$word	[string]	The word you want to check for
		**	@return	[bool]	Returns true on valid, false if wrong spelling
		*/
		public	function spellCheckWord($word)
			{
				// Assign the dictionary
				$dic	=	$this->lib;
				try {
					// If not available, let user know
					if(empty($dic))
						throw new \Exception($this->errorMsg('1005'));
				}
				catch (\Exception $e){
					die($e->getMessage());	
				}
				// Check if the word is valid
				return (pspell_check($dic,$word));
			}
		/*
		**	@description	This method splits up a string by spaces
		**	@param	$word	[string]	The word you want to check for
		**	@param	$wrap	[array]	This is an array containing begin and end wrapper values
		**	@return	[array]	Returns an array of suspect words
		*/
		private	function splitWords($word,$wrap)
			{
				// Explode the string by spaces
				$split	=	explode(' ',$word);
				$new	=	array();
				if(is_array($split)) {
					foreach($split as $word) {
						$word	=	preg_replace('/[^a-zA-Z0-9\-]/','',trim($word));
						if(!$this->spellCheckWord($word)) {
							$word	=	$wrap[0].$word.$wrap[1];
						}
						
						$new[]	=	$word;
					}
				}
				
				return $new;
			}
		/*
		**	@description	This will split a block of text into paragraph blocks, if need be,
							and get list of suspect words
		**	@param	$word	[string]	The word you want to check for
		**	@param	$wrap	[array]	This is an array containing begin and end wrapper values
		**	@return	[array]	Returns an array of suspect words
		*/
		public	function spellCheckStr($word,$wrap)
			{
				$new	=	array();
				if(is_string($word)) {
					$pSplit	=	explode(PHP_EOL,$word);
					if(is_array($pSplit)) {
						foreach($pSplit as $paragraph) {
							$new	=	array_merge($new,$this->splitWords($paragraph,$wrap));
						}
					}
					else
						$new	=	$this->splitWords($word,$wrap);
				}
				
				return $new;
			}
		/*
		**	@description	This will retrieve suspect words, check if there are any allowed words, then replace
							any found with html wrappers (span by default)
		**	@param	$word	[string]	The word you want to check for
		**	@param	$wrap	[array]	This is an array containing begin and end wrapper values
		**	@param	$rWrap	[array]	This is an array containing begin and end wrapper HTML values
		**	@return	[string]	Returns string with possible html wrappers
		*/
		public	function spellCheckBlock($word,$rWrap = array('{','}'), $wrap = array('<span class="wrongwrd">','</span>'))
			{
				// Get suspect words
				$block	=	$this->spellCheckStr($word,$rWrap);
				// If there are none, just return string
				if(empty($block))
					return $word;
				// Loop through the words and find words that have wrap values
				$filter	=	array_map(function($v) use ($rWrap)
					{
						if(strpos($v,$rWrap[0]) !== false && strpos($v,$rWrap[1]) !== false)
							return $v;
						else
							return false;
							
					},$block);
				// Filter empty and unique values
				$filter	=	array_unique(array_filter($filter));
				// If there are remaining words
				if(!empty($filter)) {
					// Loop though the words and and trim off brackets
					foreach($filter as $key => $bWord) {
						$filter[$key]	=	rtrim(ltrim($bWord,'{'),'}');
					}
				}
				// If there are any words that should be allowed that may be unique or not-in-dictionary words
				if(is_array($this->addWords) && !empty($this->addWords)) {
					// If there is a case sensitivity on
					if($this->case) {
						// Loop through add words and make lower
						foreach($this->addWords as $fWord) {
							$cArr[]	=	strtolower($fWord);
						}
						// Loop through filter words and make lower 
						foreach($filter as $key => $value) {
							// See if the word is in the suspect words.
							// If so, unset
							if(in_array(strtolower($value),$cArr))
								unset($filter[$key]);
						}
					}
					else
						// Just subtract array
						$filter	=	array_diff($filter,$this->addWords);
				}
				// If filter empty, just return words
				if(empty($filter))
					return $word;
				// If not empty, return the callback
				return preg_replace_callback('/'.implode('|',$filter).'/',function($v) use ($wrap)
					{
						$v[0]	=	trim($v[0]);
						
						if(!empty($v[0]))
							return $wrap[0].$v[0].$wrap[1];
					},$word);
			}
		/*
		**	@description	This method is like adding unique words to the filter so it doesn't mark as misspelled
		**					An example would be: supercalafragalisticexpialidocious
		**	@param	$added	[array]	Unordered array of words to skip
		**	@param	$case	[bool]	If set to true, the spellCheckBlock() method will do two foreach loops to find
		**							same-as words with case-insentive in mind, otherwise it is a quicker array_diff()
		**							native php function to subtract added words
		*/
		public	function addWords($added,$case = false)
			{
				// If left to false, use array_diff()
				$this->case		=	$case;
				// Assign words array
				$this->addWords	=	$added;
				return $this;	
			}
		/*
		**	@description	This will suggest words for $word
		*/
		public	function suggest($word)
			{
				return pspell_suggest($this->lib,$word);
			}
		/*
		**	@description	Save a list of unique words to add into default dictionary
		**	@param	$dir	[string]	This is where the file will be saved
		**	@param	$filename	[string]	This is the name of the file
		**	@param	$lang	[string]	This is the name of the dictionary language
		*/
		public	function savePersonalFile($dir,$filename,$lang = 'en')
			{
				if(!is_dir($dir)) {
					if(@!mkdir($dir,0755,true)) {
						throw new \Exception($this->errorMsg('1006',$dir));
					}
				}
				// Open dictionary with language
				$config	=	pspell_config_create($lang);
				// Create personal config in directory
				pspell_config_personal($config, str_replace(_DS_._DS_,_DS_,$dir._DS_.$filename.'.pws'));
				// Create config using personal
				$this->lib	=	pspell_new_config($config);
				// Return method for chaining
				return $this;
			}
		/*
		**	@description	This stores a word to persist through the class
		**	@param	$word	[string]	The word
		*/
		public	function useWord($word)
			{
				$this->word	=	$word;
				return $this;
			}
		/*
		**	@description	This method will create a word placeholder for a personal file
		**	@param	$word	[string]	This is the word to store
		**	@requires		This requires a valid dictionary
		*/
		public	function addToWordList($word = false)
			{
				if(!empty($word))
					$this->word	=	$word;
				else {
					$this->word	=	(!empty($this->word))? $this->word : false;
				}
				
				try {
					if(empty($this->lib))
						throw new \Exception($this->errorMsg('1002',array('None set/Unknown: '.__LINE__)));
					elseif(empty($this->word))
						throw new \Exception($this->errorMsg('1007',__FUNCTION__));
						
					pspell_add_to_personal($this->lib,$this->word);
					
					return $this;
				}
				catch (\Exception $e) {
					die($e->getMessage());
				}
			}
		/*
		**	@description	This will store one or more words into file
		**	@param	$array	[array|string]	Word or words to store
		**	@requires		This requires a valid personal link
		*/
		public	function learnWords($array)
			{
				if(empty($this->personal))
					return false;

				if(is_string($array))
					$array	=	array($array);
				
				foreach($array as $value) {
					$this->useWord($value)->addToWordList();
				}
				// Save all the words into the personal file
				$this->saveWordList();
			}
		/*
		**	@description	This is run at the end of a list save.
		**	@requires		This requires a valid dictionary resource to be initiated
		*/
		public	function saveWordList()
			{
				if(empty($this->lib))
					throw new \Exception($this->errorMsg('1002',array('None set/Unknown: '.__LINE__)));
					
				@pspell_save_wordlist($this->lib);
			}
		/*
		**	@description	This method returns error messages
		*/
		private	function errorMsg($code,$data = false)
			{
				$array['1000']	=	'Unknown error.';
				$array['1001']	=	'Pspell Library not activated. Spellcheck unavailable.';
				$array['1002']	=	'Spelling resource failed to initialize using "'.implode('"<br />"',$data).'" Library, check settings.';
				$array['1003']	=	'Spelling resource failed to initialize using settings: "'.implode('"<br />"',$data).'". Check settings.';
				$array['1004']	=	'You must have a '.$data[0].' set: '.$data[1];
				$array['1005']	=	'You must assign a library in __contruct(\'en\') or use method useDictionary(\'en\')';
				$array['1006']	=	'Failed to create directory: "'.$data.'"';
				$array['1007']	=	'Word can not be empty: "'.$data.'"';
				
				return (isset($array[$code]))? $array[$code] : $array['1000'];
			}
	}