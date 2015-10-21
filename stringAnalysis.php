<?php
	/**
	 * this class handles all string analysis
	 */
	class stringAnalysis {
		/**
		* Determines the first occuring, most frequent letter in word
		*
		* @param string $word the word to analyze
		* @return array $result information on the most frequent first-occuring letter with keys:
		* letter, count = the number of times the letter occuers in the word
		*/
		function mostFreqLetter ($word){
			// create return array, split out letters into array
			$result = ['letter'=>'','count'=>0];
			$wordLetters = str_split($word);
			// loop through letters, updating the most frequent letter in result as neccesary. stop analyzing if remaining letters less than current winning letter frequency. 
			for ($i=0;$i<count($wordLetters) && count($wordLetters)-$i >= $result['count'];$i++){
				// get count of letter in word, if greater than the current winning frequency, update result.
				$cLetterCount = substr_count($word,$wordLetters[$i]);	
				if ($cLetterCount > $result['count']){
					$result['count'] = $cLetterCount;
					$result['letter'] = $wordLetters[$i];
				}
			}
			return $result;
		}
		/**
		 * Takes a string, removes file formatting chars, transforms into a multi-level word array sorted by "clean" word length desc
		 * 
		 * @param string $string the string to clean & sort
		 * @return array $wordArray words in the string, sorted by clean length desc with the following keys:
		 * opos = original position of word, clean = word stripped of non-alpha chars, dirty = word as input
		 */
		function cleanSortString($string){
			// clean out any possible file formatting characters from string
			// todo: fix regexp - it can consodliated into one expression. 
			$string = trim(preg_replace('/ {2,}/',' ',preg_replace('/[\r\n\t\f\v]/',' ',$string)));
			// turn the string into an array of words
			$wordArray = explode(' ',$string);
			// loop $wordArray, replace each key with array containing keys listed in method comment
			for ($i=0;$i<count($wordArray);$i++){
				$wordArray[$i] = array('opos'=>$i,'clean'=>strtolower(preg_replace('/[^a-zA-Z]/', '', $wordArray[$i])),'dirty'=>$wordArray[$i]);
			}
			// sort the $wordArray desc on length of clean string
			// todo: Check on efficency of this sorting. 
			function custComp($a,$b){
				return strlen($b['clean'])-strlen($a['clean']);
			}
			usort($wordArray,'custComp');
			return $wordArray;
		}
		/**
		 * main function used to determine which first-occuring word has a letter with the most frequent occurances, out of every other word
		 * 
		 * @param array $wordsString = a string of words to be analyzed
		 * @return array $winningWord = an array containing information about the first-occuring word with the highest frequency letter occurance out of any other word - having the following keyss: 
		 * letter = most frequent letter, word = the word, count = the count of the first-occuring most frequent letter, opos = the original position of the word
		 */
		function getWinningWordLetter($wordsString){
			// clean and sort the word string
			$wordArray = $this->cleanSortString($wordsString);
			// pepare result structure
			$winningWord = array('letter'=>'','word'=>'','count'=>0,'opos'=>0);
			// loop $wordArray, don't analyze words with lower length than current winning letter frequency
			for ($i=0;$i<count($wordArray) && $winningWord['count'] <= strlen($wordArray[$i]['clean']);$i++){
				// following values are used multiple times, put in var to avoid redundant array lookup
				$thisCleanWord = $wordArray[$i]['clean'];
				$thisOrigPos = $wordArray[$i]['opos'];
				// dont try and analyze if clean value is empty
				if (strlen($thisCleanWord)){
					// get first occuring, most frequent letter in word.
					$thisWinningLetter = $this->mostFreqLetter($thisCleanWord);
					// set new winning word if this words most frequent letter occurs more than the current winning letter frequency, or if the letter frequency is a tie with the winner but was originally positined before winner
					if ($thisWinningLetter['count']>$winningWord['count'] || ($thisWinningLetter['count']===$winningWord['count'] && $thisOrigPos < $winningWord['opos'])){
						// save letter, user input word (Dirty), count and position for later use
						$winningWord['letter'] = $thisWinningLetter['letter'];
						$winningWord['word'] = $wordArray[$i]['dirty'];
						$winningWord['count'] = $thisWinningLetter['count'];
						$winningWord['opos'] = $thisOrigPos;
					}
				}
			}
			return $winningWord;
		}
	}
?>