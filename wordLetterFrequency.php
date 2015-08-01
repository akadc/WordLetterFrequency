<?php

	function wordFind($path){

		// only continue if path to valid & accessible file is being passed 
		if (strlen($path) && is_readable($path)){

			// get file content, strip; any file formatting chars, any 2 spaces in row, file start / end space
			$fileContent = trim(preg_replace('/ {2,}/',' ',preg_replace('/[\r\n\t\f\v]/',' ',file_get_contents($path))));

			// only continue if there is actually usable content in the file
			if (strlen($fileContent)){

				// split words into array
				$wordAnalysis = explode(' ',$fileContent);

				// retain user input word & position, clean non alpha chars, set case of clean chars, count clean chars
				for ($i=0;$i<count($wordAnalysis);$i++){
					$wordAnalysis[$i] = array('opos'=>$i,'clean'=>strtolower(preg_replace('/[^a-zA-z]/', '', $wordAnalysis[$i])),'dirty'=>$wordAnalysis[$i]);
				}

				// sort desc on clean string length 
				function custComp($a,$b){
					return strlen($b['clean'])-strlen($a['clean']);
				}
				usort($wordAnalysis,'custComp');

				// prepare final result strcuture
				$winningWord = array('letter'=>'','word'=>'','count'=>0,'opos'=>0);
			
				// determine the first-occuring word with the most frequent occurance of any one letter, out of entire word set. don't analyze words with lower length than current winning letter frequency
				for ($ii=0;$ii<count($wordAnalysis) && $winningWord['count'] <= strlen($wordAnalysis[$ii]['clean']);$ii++){

					// following values are accessed more than once, so save to separate var
					$cCleanWord = $wordAnalysis[$ii]['clean'];
					$cOPOS = $wordAnalysis[$ii]['opos'];

					// split letters into array
					$cWordLetters = str_split($cCleanWord);
					// prepare winning letter array
					$cWinningLetter = array('letter'=>'','count'=>0);
				
					// determine the first occuring, most frequent letter in word. stop analyzing letters if remaning letters to analyze is less than current winning letter frequency for this word
					for ($iii=0;$iii<count($cWordLetters) && count($cWordLetters)-$iii >= $cWinningLetter['count'];$iii++){
			
						// get count of letter in word
						$cLetterCount = substr_count($cCleanWord,$cWordLetters[$iii]);
						// if highest frequency so far, record it
						if ($cLetterCount > $cWinningLetter['count']){
							$cWinningLetter['count'] = $cLetterCount;
							$cWinningLetter['letter'] = $cWordLetters[$iii];
						}
					}

					// if highest frequency letter for current word is greater than any other analyzed word + word was positioned first by user set letter / word as current high
					if ($cWinningLetter['count']>$winningWord['count'] && (!!is_int($winningWord['opos']) || $cOPOS < $winningWord['opos'])){
						// save letter, user input word (Dirty), count and position for later use
						$winningWord['letter'] = $cWinningLetter['letter'];
						$winningWord['word'] = $wordAnalysis[$ii]['dirty'];
						$winningWord['count'] = $cWinningLetter['count'];
						$winningWord['opos'] = $cOPOS;
					}

				}

				$result['error'] = '';
				$result['winningWord'] = $winningWord;

			}

			// no text to analyze
			else {
				$result['error'] = 'text file has nothing to analyze';
			}

		}

		// file was unreadable
		else {

			$result['error'] = 'please specify a valid file which is readable by PHP';
		}

		return $result;
	}

	// file path to edit
	$filePath = 'words.txt';
 
	print '<!doctype html><html><head><title></title></head><body>';

	// if no file path, inform
	if(strlen($filePath)===0){

		print '<p>edit $filePath var in this file and re-run</p>';

	}

	// there is file path, attempt to analyze
	else {

		$finalResult = wordFind($filePath);

		// if error
		if (strlen($finalResult['error'])){

			print 'Error: ' . $finalResult['error'];

		}

		// no error
		else {

			//nothing usable
			if ($finalResult['winningWord']['count']===0){

				print '<p>Text file was analyzed, but no qualified letters / words were found</p>';

			}
			else {

				print '<p>"'. $finalResult['winningWord']['word'] . '" is the first occuring word in which no other subsequent word has a greater frequency of any one letter ("'. $finalResult['winningWord']['letter'] . '" occurs ' . $finalResult['winningWord']['count'] . ' times in the word.)';
			
			}
		
		}

	}

	print '</body></html>'

?>