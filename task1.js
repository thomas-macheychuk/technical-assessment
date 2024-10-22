/**
 * Checks if two strings are anagrams of each other.
 *
 * An anagram is a word or phrase formed by rearranging the letters of a different word or phrase,
 * such as 'lamp', formed from 'palm'.
 *
 * This function ignores whitespace and letter casing when determining if the strings are anagrams.
 *
 * @param {string} str1 The first string.
 * @param {string} str2 The second string.
 * @returns {boolean} True if the strings are anagrams, false otherwise.  

 *
 * @example
 * isAnagram("tom", "mot") // returns true
 * isAnagram("Tom Marvolo Riddle", "I am Lord Voldemort") // returns true
 * isAnagram("Hello", "World") // returns false
 */
function isAnagram(str1, str2) {
    // Removing whitespace and converting strings to lowercase
    str1 = str1.replace(/\s/g, '').toLowerCase()
    str2 = str2.replace(/\s/g, '').toLowerCase()

    // Determine if the strings have the same length. If they do not, return false
    if(str1.length !== str2.length) {
        return false
    }

    // Splitting, sorting, and joining the strings to check if they have the same characters with the same frequency
    str1 = str1.split('').sort().join('')
    str2 = str2.split('').sort().join('')

    return str1 === str2
}
