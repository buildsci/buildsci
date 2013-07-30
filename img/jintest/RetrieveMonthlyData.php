<?php

function retriveMonthlyData($path,$htmlFile,$title,$selectColumn,$totalColumn) {
	$isTgTable = 0;                                   // 0: is not the target table, 1: is the target table 

	// the input file information
	$filePath = $path;									  
	$filename  = $path.'/'.$htmlFile;                 // the name and location of the input file

	// the current row and column
	$curCol    = 0;									  // the current column
	$curRow    = 0;									  // the current row

	// file open
	$fh = fopen($filename, "r");					  // the read file handler 

	// cell information
	$key = "";
	$newValue;

	// talbe information
	$tableHead = "/\<p\>Report:\<b\> BUILDING MONTHLY COOLING LOAD REPORT\<\/b\>\<\/p\>/";     //  "/<b\>".$tableName."\<\/b\>/";
	$zoneField = "/\<p\>For:\<b\> THERMAL ZONE [0-9]+\<\/b\>\<\/p\>/";

	$tableTail = "/\<\/table\>/";
	
	// get table information from the file 
	if($fh) {
    // getline 
		while (($buffer = fgets($fh, 4096)) != false ) {

			// found the taget table
			if (preg_match($tableHead, $buffer)){  
				//print '!!!!!!!!!table head: '.$buffer.'!!!!!!!!!';				
				$isTgTable=1;
		  }
		
      // found the zone field
			if (preg_match($zoneField, $buffer)){
				$pattern='/\<p\>For:\<b\> THERMAL ZONE ([0-9]+)\<\/b\>\<\/p\>/';
				$replacement = '$1';
				$zone = str_replace("\n", "", preg_replace($pattern,$replacement, $buffer));
				//print '##########zone field: '.$zone.'##########\n';
			}

			// select the right table to print out
			if ($isTgTable == 1) {

				// found the tail of table, and stop printing
				if (preg_match($tableTail, $buffer))
					$isTgTable = 0;

				// updating current row and colum
				preg_match("/\<\/td\>/", $buffer) ? $curCol++ : $curCol;		
				preg_match("/\<\/tr\>/", $buffer) ? $curRow++ : $curRow;
			
				// Key is at the first column on the table
				if(($curCol%$totalColumn) == 1) {
					
					$pattern='/^.*\>(.*)\<.*/i';
					$replacement = '$1';
					$key = str_replace("\n", "", preg_replace($pattern,$replacement, $buffer));
					//echo $key;
				}
			
				// Value Column
				if(($curCol%$totalColumn) == $selectColumn) {

					$pattern='/^.*\>(.*)\<.*/i';
					$replacement = '$1';
                    
					$newValue[$key.'Zone'.$zone.'Column'.$selectColumn] = str_replace(" ", "", preg_replace($pattern,$replacement, $buffer));
				}
			}
		}
		
		// check if the file ends successfully
		if (!feof($fh)) {
			echo "Error: unexpected fgets fail \n";
		}
	}

	// close file
	fclose($fh);

	return $newValue;
}

// Test RetrieveMonthlyData
$data= retriveMonthlyData("./","eplustbl.htm","title",3,5);

// $data[$key.$zone.$selectColumn]
echo 'January Zone 1-15, SYSSENSIBLECOOLINGENERGY[J]';

$month = array('January',
         'February',
         'March',
         'April',
         'May',
         'June',
         'July',
         'August',
         'September',
         'October',
         'November',
         'December');
    

// store the data to array
$zone = 3;
for($i=0; $i<12; $i++)
{
   print 'zone: '.$zone.', '.$month[$i].'  >>  '.$data[$month[$i].'Zone'.$zone.'Column3'];
    
}		

?>
