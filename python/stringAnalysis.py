class StringAnalysis:
	"""this class handles all string analysis"""

	def mostFreqLetter(self,word):	
		""" Determines the first occuring, most frequent letter in word

		inputs: word (string) = the word to analyze
		returns: result (dict) = information on the most frequent first-occuring letter with keys:
		letter, count = the number of times the letter occuers in the word"""
		#create return dict
		result = {'letter':'','count':0}
		# loop through letters, updating the most frequent letter in result as neccesary. stop analyzing if remaining letters less than current winning letter frequency. 
		for i in range(0,len(word)):
			# if remaining letters less than current winner, stop checking
			if len(word)-i < result['count']:
				break
			# get count of letter in word, if greater than the current winning frequency, update result.
			# todo: this should be smart enough to not check the same letter twice
			# todo: evaluate efficency of substring access, since letter and its count in word could be accessed multiple times
			if word.count(word[i]) > result['count']:
				result['count'] = word.count(word[i])
				result['letter'] = word[i]
		return result
	def cleanSortString(self,string):
		""" Takes a string, removes file formatting chars, transforms into a list with nested dictionaries sorted by "clean" word length desc

		 inputs: string (string) = the string to clean & sort
		 returns: wordList (list) = a list of words in the string, represented by dicts, sorted by clean length desc with the following keys:
		 opos = original position of word, clean = word stripped of non-alpha chars, dirty = word as input"""
		# import regexp module
		import re
		# clean out any possible file formatting characters from string
		# todo: fix regexp - it can consodliated into one expression. 
		string = re.sub(' {2,}',' ',re.sub('[\r\n\t\f\v]',' ',string))
		# split words into a list
		wordList = string.split(' ')
		# compile regexp as its being used for every loop
		wordCleanRE = re.compile('[^a-zA-Z]')
		# loop wordList, replace each item with dict containing keys listed in method comment
		for i in range(0,len(wordList)):
			wordList[i] = {'opos':i,'clean':re.sub(wordCleanRE,'',wordList[i]),'dirty':wordList[i]}
		# todo: look into rules regarding nested methods
		def getCleanLength(word):
			return len(word['clean'])
		# sort the wordList desc on length of clean string
		# todo: check on efficency of this sorting.
		wordList = sorted(wordList,key=getCleanLength,reverse=True)
		return wordList
	def getWinningWordLetter(self,wordsString):
		"""main function used to determine which first-occuring word has a letter with the most frequent occurances, out of every other word
		 
		 inputs: wordsString (string) = a string of words to be analyzed
		 returns: winningWord (dict) = an dict containing information about the first-occuring word with the highest frequency letter occurance out of any other word - having the following keys: 
		 letter = most frequent letter, word = the word, count = the count of the first-occuring most frequent letter, opos = the original position of the word"""
		# clean and sort the word string
		wordDict = self.cleanSortString(wordsString)
		# create the result dict
		winningWord = {"word":"","letter":"","count":0,"opos":0}
		# loop the sorted words
		for i in range(0,len(wordDict)):
			# used multiple times, put in var to avoid redundant dict lookup
			# todo: eval dict optimization to see if this is neccesary
			thisCleanWord = wordDict[i]['clean']
			# if current winning letter frequency is greater than length of word to be analyzed, break loop (wordDict is sorted on clean word length desc)
			if winningWord['count'] > len(thisCleanWord):
				break
			# used multiple times, put in var to avoid redundant dict lookup
			# todo: eval dict optimization to see if this is neccesary
			thisOrigPos = wordDict[i]['opos']
			# dont try and analyze if clean value is empty
			# todo: eval if this if should be moved higher up
			if len(thisCleanWord):
				# get first occuring, most frequent letter in word
				thisWinningLetter = self.mostFreqLetter(thisCleanWord)
				# set new winning word if this words most frequent letter occurs more than the current winning letter frequency, or if the letter frequency is a tie with the winner but was originally positined before winner
				if thisWinningLetter['count'] > winningWord['count'] or (thisWinningLetter['count'] == winningWord['count'] and thisOrigPos < winningWord['count']):
					winningWord['letter'] = thisWinningLetter['letter']
					winningWord['word'] = wordDict[i]['dirty']
					winningWord['count'] = thisWinningLetter['count']
					winningWord['opos'] = thisOrigPos
		return winningWord