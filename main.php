<?

/**
 * Takes floating point seconds (e.g 0.64) and converts them to webVTT seconds (e.g. 00:00:0.64)
 */
function secondsToWebVtt($seconds) {
	$seconds = floatval($seconds);
	$hours = floor($seconds / 3600);
	$minutes = floor($seconds / 60) - $hours * 60;
	$seconds = $seconds - $hours*3600 - $minutes*60;
	return number_format($hours, 0, '.', '') . ':' . number_format($minutes, 0, '.', '') . ':' . number_format($seconds, 2, '.', '');
}

/**
 * A function to take in awsTranscribe JSON and make it into webVTT
 */
function awsTranscribeToWebVtt($jsonString) {
	//Number of characters to break on for after punctuation/not after punctuation
	$WORD_BREAK_WITH_PUNCTUATION = 30;
	$WORD_BREAK_WITHOUT_PUNCTUATION = 50;

	//Inputs
	$jsonObj = json_decode($jsonString);
	$words = $jsonObj->results->items;

	//Outputs
	$returnString = "WEBVTT\n\n";

	//Variables for loop
	$currentString = '';
	$startTime = null;
	$endTime = 0;
	$wordBreak = false;
	//Loop over every word/punctuation mark
	foreach($words as $word) {
		if (is_null($word->start_time)) {
			if($word->type === "punctuation") {
				$currentString .= $word->alternatives[0]->content;
				$wordBreak = (strlen($currentString) >= $WORD_BREAK_WITH_PUNCTUATION);
			}
		}
		else {
			if (is_null($startTime)) {
				$startTime = $word->start_time;
			}
			$endTime = $word->end_time;
			$currentString .= ($currentString == '' ? '' : ' ') . $word->alternatives[0]->content;
			$wordBreak = (strlen($currentString) >= $WORD_BREAK_WITHOUT_PUNCTUATION);
		}

		if($wordBreak) {
			$returnString .= secondsToWebVtt($startTime) . ' --> ' . secondsToWebVtt($endTime) . "\n";
			$returnString .= $currentString . "\n\n";
			$startTime = null;
			$currentString = '';
		}
	}

	return $returnString;
}


function test($filename) {
	return awsTranscribeToWebVtt(file_get_contents($filename));
}

echo test('test.json');
?>
