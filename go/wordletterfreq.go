package main

import
(
        "fmt"
        "stringanal"
        "io/ioutil"
        "errors"
        "flag"
)

func main() {
	// get the filepath from the command line & pass off for analysis
	filePathPtr := flag.String("filepath","","path to file containing words to analyze")
	flag.Parse()
	filePath := *filePathPtr
	result, error := retreiveWordAnalysis(filePath)
	if error != nil {
		fmt.Println("error: ",error)
	} else {
		fmt.Println("result: ",result)
	}
	
}

func retreiveWordAnalysis(filePath string) (string, error){
	// if no file path is provided 
	if len(filePath) == 0{
		return "", errors.New(`please provide a text file path with the "filepath" flag`)
	}
	// attempt to read file
	fileContents, fileError := ioutil.ReadFile(filePath)
	// if error reading file 
 	if fileError != nil {
 		return "", errors.New("There was an error reading the file you provided, check that the file exists with the correct permissions")
 	}
 	// if file didn't have contents
 	if len(fileContents) == 0 {
 		return "", errors.New("supplied file doesn't have usable content")
 	}
	fileString := string(fileContents)
	// get the winning word
	winningWord := stringanal.GetWinningWordLetter(fileString)
	// no winning word determined from text file
	// todo: basically this occurs whent here is no alpha chars, maybe check first?
	if len(winningWord.Word) == 0 {
		return "", errors.New("word analysis function couldn't determine outcome")
	}
	// return the winning word
	return fmt.Sprintf(`%s is the first occuring word in which no other word has a greater frequency of any one letter ("%s" occurs %d times in the word.)`,winningWord.Word,winningWord.Letter,winningWord.Count), nil
}