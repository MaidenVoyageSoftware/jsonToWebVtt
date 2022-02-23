# jsonToWebVtt
Converts from AWS Transcribe JSON to WebVTT

# Example Usage
Get the vtt file as a string
```
<?
include 'main.php';

echo awsTranscribeToWebVtt(file_get_contents('test.json'));
?>
```
Output the vtt file
```
<?
include 'main.php';

$fileIn = 'test.json';
$fileOut = 'test.vtt';
$vtt = awsTranscribeToWebVtt(file_get_contents($fileIn));
file_put_contents($fileOut, $vtt);
?>
```
# Configuration
To configure location of line breaks, change the variables $LINE_BREAK_WITH_PUNCTUATION and $LINE_BREAK_WITHOUT_PUNCTUATION located in the function awsTranscribeToWebVtt in main.php

update