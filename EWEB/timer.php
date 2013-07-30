<?php 

//Page process time calculation 
/* 
The microtime() function fetches the current current Unix timestamp with microseconds 

another alternative method is to use the microtime() so that it returns a float value 
$time=microtime(1); 
this will return the current Unix timestamp like this 1113928831.585405 
*/ 
//record the starting time 
$start=microtime(); 
$start=explode(" ",$start); 
$start=$start[1]+$start[0]; 

sleep(2);//halt the script for 2 seconds 
//I put this just to affect the final page process time output...u will have your code here.. 

//record the ending time 
$end=microtime(); 
$end=explode(" ",$end); 
$end=$end[1]+$end[0]; 

printf("Page was generated by PHP %s in %f seconds",phpversion(),$end-$start); 

//sample output: Page was generated by PHP 5.0.3 in 1.987188 seconds 

?>