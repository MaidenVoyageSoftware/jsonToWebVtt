<?

/**
 * Takes floating point seconds (e.g 0.64) and converts them to webVTT seconds (e.g. 00:00:0.64)
 */
function secondsToWebVtt($seconds) {
	$seconds = floatval($seconds);
	$hours = floor($seconds / 3600);
	$minutes = floor($seconds / 60) - $hours * 60;
	$seconds = $seconds - $hours*3600 - $minutes*60;
	$hours = number_format($hours, 0, '.', '');
	$hours = str_repeat('0', 2 - strlen($hours)) . $hours;
	$minutes = number_format($minutes, 0, '.', '');
	$minutes = str_repeat('0', 2 - strlen($minutes)) . $minutes;
	$seconds = $seconds !== 0 ? (($seconds < 10 ? '0' : '') . number_format($seconds, 3, '.', '')) : '00.00';
	return $hours . ':' . $minutes . ':' . $seconds;
}

//If it is time for a new line, push these words to the return string
//Format (substitute in times):
//```
//st:ar:ti.me --> en:dt:im.e0
//Text for 30 to 50 characters
//
//```
function handleWordBreak($startTime, $endTime, $currentString) {
	return secondsToWebVtt($startTime) . ' --> ' . secondsToWebVtt($endTime) . "\n$currentString\n\n";
}

/**
 * A function to take in awsTranscribe JSON and make it into webVTT
 */
function awsTranscribeToWebVtt($jsonString) {
	//Number of characters to break on for after punctuation/not after punctuation
	$LINE_BREAK_WITH_PUNCTUATION = 30;
	$LINE_BREAK_WITHOUT_PUNCTUATION = 50;

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
		//if the word is actually punctuation, handle it
		if (is_null($word->start_time)) {
			if($word->type === "punctuation") {
				$currentString .= $word->alternatives[0]->content;
				//If we overflow this time, set the currentString to '' and push everything to returnString
				if (strlen($currentString) >= $LINE_BREAK_WITH_PUNCTUATION) {
					$returnString .= handleWordBreak($startTime, $endTime, $currentString);
					$startTime = null;
					$currentString = '';
				}
			}
		}
		//Otherwise, we have a word
		else {
			if (is_null($startTime)) {
				$startTime = $word->start_time;
			}
			$tempString = $currentString . ($currentString == '' ? '' : ' ') . $word->alternatives[0]->content;
			//If we overflow this time, set the currentString to 'thisWord' and push everything except this word to returnString
			//This makes it so that punctuation isn't by itself on a new line.
			if (strlen($tempString) >= $LINE_BREAK_WITHOUT_PUNCTUATION) {
				$returnString .= handleWordBreak($startTime, $endTime, $currentString);

				$startTime = $word->start_time;
				$endTime = $word->end_time;
				$currentString = $word->alternatives[0]->content;
			}
			else {
				$endTime = $word->end_time;
				$currentString = $tempString;
			}
		}
	}

	//Catch the words in currentString after the loop finishes
	if($currentString !== '') {
		$returnString .= handleWordBreak($startTime, $endTime, $currentString);
	}

	return $returnString;
}
?>
