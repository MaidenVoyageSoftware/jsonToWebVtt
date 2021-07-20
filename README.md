# jsonToWebVtt
Converts from AWS Transcribe JSON to WebVTT

# Example Usage
```
<?
include 'main.php';

echo awsTranscribeToWebVtt(file_get_contents('test.json'));
?>
```
# Configuration
To configure location of line breaks, change the variables $LINE_BREAK_WITH_PUNCTUATION and $LINE_BREAK_WITHOUT_PUNCTUATION located in the function awsTranscribeToWebVtt in main.php
