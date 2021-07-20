<?
include 'main.php';

echo awsTranscribeToWebVtt(file_get_contents('test2.json'));
/*echo secondsToWebVtt(0) . "\n";
echo secondsToWebVtt(61) . "\n";
echo secondsToWebVtt(3661) . "\n";
echo secondsToWebVtt(30) . "\n";
echo secondsToWebVtt(9) . "\n";
echo secondsToWebVtt(10) . "\n";*/
?>
