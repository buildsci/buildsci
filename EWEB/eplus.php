
<?php
// ini_set('max_execution_time', 120); //120 seconds = 2 minutes

//record the starting time 
$start=microtime(); 
$start=explode(" ",$start); 
$start=$start[1]+$start[0]; 

echo '<pre>';
echo passthru('dir');

echo '<hr />';
chdir ('../');
echo passthru('dir');
echo '<hr />';

chdir('EnergyPlus-7-2-0');
echo passthru('dir');
echo '<hr />';
chdir('bin');
echo passthru('dir');
echo '<hr />';

$output = shell_exec('./runenergyplus ../../Buildings/HUB55.idf ../../Weather/USA_CA_San.Francisco.Intl.AP.724940_TMY3.epw');
//minimalIDF

echo $output.'</pre>';

//record the ending time 
$end=microtime(); 
$end=explode(" ",$end); 
$end=$end[1]+$end[0]; 

printf("Page was generated by PHP %s in %f seconds",phpversion(),$end-$start); 

?>

