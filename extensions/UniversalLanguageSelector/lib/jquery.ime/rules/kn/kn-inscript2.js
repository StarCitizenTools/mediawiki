( function ( $ ) {
	'use strict';

	var knInscript2 = {
		id: 'kn-inscript2',
		name: 'ಇನ್\u200cಸ್ಕ್ರಿಪ್ಟ್ ೨',
		description: 'Enhanced InScript keyboard for Kannada script',
		date: '2013-01-16',
		author: 'Parag Nemade',
		license: 'GPLv3',
		version: '1.0',
		patterns: [
			[ '1', '೧' ],
			[ '2', '೨' ],
			[ '\\#', '್ರ' ],
			[ '3', '೩' ],
			[ '\\$', 'ರ್' ],
			[ '4', '೪' ],
			[ '5', '೫' ],
			[ '6', '೬' ],
			[ '7', '೭' ],
			[ '8', '೮' ],
			[ '\\(', '(' ],
			[ '9', '೯' ],
			[ '\\)', ')' ],
			[ '0', '೦' ],
			[ '\\_', 'ಃ' ],
			[ '\\-', '-' ],
			[ '\\+', 'ಋ' ],
			[ '\\=', 'ೃ' ],
			[ 'Q', 'ಔ' ],
			[ 'q', 'ೌ' ],
			[ 'W', 'ಐ' ],
			[ 'w', 'ೈ' ],
			[ 'E', 'ಆ' ],
			[ 'e', 'ಾ' ],
			[ 'R', 'ಈ' ],
			[ 'r', 'ೀ' ],
			[ 'T', 'ಊ' ],
			[ 't', 'ೂ' ],
			[ 'Y', 'ಭ' ],
			[ 'y', 'ಬ' ],
			[ 'U', 'ಙ' ],
			[ 'u', 'ಹ' ],
			[ 'I', 'ಘ' ],
			[ 'i', 'ಗ' ],
			[ 'O', 'ಧ' ],
			[ 'o', 'ದ' ],
			[ 'P', 'ಝ' ],
			[ 'p', 'ಜ' ],
			[ '\\{', 'ಢ' ],
			[ '\\[', 'ಡ' ],
			[ '\\}', 'ಞ' ],
			[ '\\]', '಼' ],
			[ 'A', 'ಓ' ],
			[ 'a', 'ೋ' ],
			[ 'S', 'ಏ' ],
			[ 's', 'ೇ' ],
			[ 'D', 'ಅ' ],
			[ 'd', '್' ],
			[ 'F', 'ಇ' ],
			[ 'f', 'ಿ' ],
			[ 'G', 'ಉ' ],
			[ 'g', 'ು' ],
			[ 'H', 'ಫ' ],
			[ 'h', 'ಪ' ],
			[ 'J', 'ಱ' ],
			[ 'j', 'ರ' ],
			[ 'K', 'ಖ' ],
			[ 'k', 'ಕ' ],
			[ 'L', 'ಥ' ],
			[ 'l', 'ತ' ],
			[ ':', 'ಛ' ],
			[ ';', 'ಚ' ],
			[ '"', 'ಠ' ],
			[ '\\\'', 'ಟ' ],
			[ '\\~', 'ಒ' ],
			[ '`\\', 'ೊ' ],
			[ 'Z', 'ಎ' ],
			[ 'z', 'ೆ' ],
			[ 'x', 'ಂ' ],
			[ 'C', 'ಣ' ],
			[ 'c', 'ಮ' ],
			[ 'v', 'ನ' ],
			[ 'b', 'ವ' ],
			[ 'N', 'ಳ' ],
			[ 'n', 'ಲ' ],
			[ 'M', 'ಶ' ],
			[ 'm', 'ಸ' ],
			[ '\\<', 'ಷ' ],
			[ ',', ',' ],
			[ '\\>', '।' ],
			[ '\\.', '.' ],
			[ '/', 'ಯ' ],
			[ '\\%', 'ಜ್ಞ' ],
			[ '\\^', 'ತ್ರ' ],
			[ '\\&', 'ಕ್ಷ' ],
			[ '\\*', 'ಶ್ರ' ]
		],
		patterns_x: [
			[ '1', '\u200d' ],
			[ '2', '\u200c' ],
			[ '4', '₹' ],
			[ '\\+', 'ೠ' ],
			[ '\\=', 'ೄ' ],
			[ 'R', 'ೡ' ],
			[ 'r', 'ೣ' ],
			[ 'u', 'ೱ' ],
			[ 'F', 'ಌ' ],
			[ 'f', 'ೢ' ],
			[ 'H', 'ೞ' ],
			[ 'j', 'ೲ' ],
			[ '\\>', 'ಽ' ],
			[ '\\.', '॥' ]
		]
	};

	$.ime.register( knInscript2 );
}( jQuery ) );
