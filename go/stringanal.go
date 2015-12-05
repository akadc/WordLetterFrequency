// package stringanalysis contains string analysis functions
package stringanal

import
(
        "strings"
        "regexp"
        "sort"
)

type WinningWord struct {
    Letter string
    Word string
    Count int
    opos int
}

type WordLetter struct {
    letter string
    frequency int
}

type Word struct {
    opos int
    dirty string
    clean string
    cleanLen int
}

type Words []Word

// setup Len, Less, Swap functions for custom sorting
func (w Words) Len() int {
    return len(w)
}

func (w Words) Less(i, j int) bool {
    return w[i].cleanLen > w[j].cleanLen;
}

func (w Words) Swap(i, j int) {
    w[i], w[j] = w[j], w[i]
}

// Determines the first occuring, most frequent letter in word
func mostFreqLetter(word string) WordLetter {
    winningLetter := WordLetter{}
    // split out word letters into an array
    wordLetters := strings.Split(word,"")
    // loop through letters, updating the most frequent letter in result as neccesary. stop analyzing if remaining letters less than current winning letter frequency
    for i := 0;i < len(wordLetters) && len(wordLetters) - i >= winningLetter.frequency; i++{
            // get count of letter in word, if greater than the current winning frequency, update winning letter.
            cLetterCount := strings.Count(word,wordLetters[i])
            if cLetterCount > winningLetter.frequency {
                winningLetter = WordLetter{letter: wordLetters[i], frequency: cLetterCount}
            }
    }
    return winningLetter
}

// Takes a string, removes file formatting chars, transforms into a slice of nested structs sorted by "clean" word length desc
func cleanSortString(stringToClean string) []Word {
    // clean out any possible file formatting characters from string
    // todo: fix regexp - it can consodliated into one expression.
    reWhitespace, reDoubleWhitespace := regexp.MustCompile("[\r\n\t\f\v]"), regexp.MustCompile(" {2,}")
    reNonAlpha := regexp.MustCompile("[^a-zA-Z]")
    cleanString := reDoubleWhitespace.ReplaceAllString(reWhitespace.ReplaceAllString(stringToClean," ")," ")
    // turn the string into a slice of individual words
    cleanStringWords := strings.Fields(cleanString)
    // create an slice to house the per word maps
    cleanSortedWords := make([]Word,len(cleanStringWords))
    // loop words array, analyze, add to per word slice map
    for i:=0;i<len(cleanStringWords);i++{
        // remove any non alpha characters from word
        cleanWord := reNonAlpha.ReplaceAllString(cleanStringWords[i],"")
        cleanSortedWords[i] = Word{opos: i, dirty: cleanStringWords[i], clean: cleanWord, cleanLen: len(cleanWord)}   
    }
    // sort word slice by clean word length desc
    sort.Sort(Words(cleanSortedWords))
    return cleanSortedWords
}

func GetWinningWordLetter(wordsString string) WinningWord {
    words := cleanSortString(wordsString)
    winningWord := WinningWord{}
    // loop the words slice, don't analyze words with lower length than current winning letter frequency
    for i:=0;i<len(words) && winningWord.Count <= words[i].cleanLen;i++{
        // following values are used multiple times, put in var to avoid redundant slice lookup
        thisCleanWord := words[i].clean
        thisOrigPos := words[i].opos
        // dont try and analyze if clean value is empty
        if len(thisCleanWord) > 0 {
            // get first occuring, most frequent letter in word
            thisWinningLetter := mostFreqLetter(thisCleanWord)
            // set new winning word if this words most frequent letter occurs more than the current winning letter frequency, or if the letter frequency is a tie with the winner but was originally positined before winner
            if thisWinningLetter.frequency > winningWord.Count || (thisWinningLetter.frequency == winningWord.Count && thisOrigPos < winningWord.opos){
                // record current winner
                winningWord = WinningWord{Letter: thisWinningLetter.letter, Word: words[i].dirty, Count: thisWinningLetter.frequency, opos: thisOrigPos}
            }
        }
    }
    return winningWord
}