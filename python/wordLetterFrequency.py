# This script allows a user to input a text file path, and have the text analyzed to find the first-occuring word with the
# highest frequnecy of any one letter out of all words in the file
# todo: assess error catching

# configuration
filePath = '../text.txt'

# import os module for file operations
import os.path
# only continue if path to file is valid & accessible
if len(filePath) and os.path.isfile(filePath):
	# read file content
	textFile = open(filePath,'r')
	fileContent = textFile.read()
	# if the file has content
	if len(fileContent):
		# get the string analysis class
		from stringAnalysis import StringAnalysis;
		sa = StringAnalysis()
		# get the winning word
		winningWord = sa.getWinningWordLetter(fileContent)
		# no winning word determined from text file
		if winningWord['count'] == 0:
			print "word analysis function couldn't determine outcome"
		# display winning word
		else:
			print '"' + winningWord['word'] + '" is the first occuring word in which no other word has a greater frequency of any one letter ("'+ winningWord['letter'] + '" occurs ' + repr(winningWord['count']) + ' times in the word.)';
	# file doesn't have content
	else:
		print "supplied file doesn't have usable content"
# file path is not accessable / readable
else:
	print "file path doesn't exist or isn't accessable"