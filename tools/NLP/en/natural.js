/*
	Pluralization
*/

if (process.argv.length !== 3) {
	console.log('Missing argument. Example usage: ' + process.argv[0] + ' ' + process.argv[1] + process.argv[2]);
	process.exit(1);
}

var natural = require('natural');
var nounInflector = new natural.NounInflector();

// Tests
// ok
// console.log(nounInflector.pluralize('fish'));
// ok
// console.log(nounInflector.pluralize('car'));
// fails
// console.log(nounInflector.pluralize('cars'));
// ok
// var car = nounInflector.singularize('cars');
// console.log(nounInflector.pluralize(car));

var word = nounInflector.singularize(process.argv[2]);
var plural = nounInflector.pluralize(word);

console.log(plural);
