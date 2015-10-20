<?php
	/**
	 * This script allows a user to input a text file path, and have the text analyzed to find the first-occuring word with the
	 * highest frequnecy of any one letter out of all words in the file
	 **/
	// set error handling to ensure it always outputs on screen
	function eHandle($enum,$emsg,$efile,$eline){
		print 'error ' . $enum . ' ' . $emsg . '(' . $efile . ':' . $eline . ')'; die();
	}
	set_error_handler('eHandle');
	try {
		// configuration 
		$filePath = 'text.txt';
		// editing below voids the warranty
		$result = '';
		// only continue if path to valid & accessible file is being passed 
		if (strlen($filePath) && is_readable($filePath)){
			// get file content 
			$fileContent = trim(file_get_contents($filePath));
			// if file has content
			if(strlen($fileContent)){
				// get the string analysis class
				include 'stringAnalysis.php';
				$stringAnalysis = new stringAnalysis();
				// get the winning word
				$winningWord = $stringAnalysis->getWinningWordLetter($fileContent);
				// report on current status of analysis:
				if ($winningWord['count'] === 0){
					throw new Exception("word analysis function couldn't determine outcome");
				}
				// display results
				else {
					print '"' . $winningWord['word'] . '" is the first occuring word in which no other word has a greater frequency of any one letter ("'. $winningWord['letter'] . '" occurs ' . $winningWord['count'] . ' times in the word.)';
				}
			}
			// file doesn't have content
			else {
				throw new Exception("supplied file doesn't have usable content");
			}
		}
		// path is not accessable / readable
		else{
			throw new Exception("file path doesn't exist or isn't accessable");
		}
	}
	catch (Exception $error){
		print $error->getMessage();
	}
?>