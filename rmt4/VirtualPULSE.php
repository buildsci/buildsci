l<?php
include 'Graph.php';
include 'readFile.php';
class VirtualPULSE  {	
	// idf file path  
	private $FilePath = './Buildings/Output/';
	
	// default version of Ruby
	private $rubyRun = 'ruby1.8';
	
	// DUMMY RUBY SCRIPT FOR GETTING DATA FROM SQL
	private $getDataRuby = 'get_data.rb';  

	// basic input information	
	private $user='';
	private $userID='';
	private $building='';
	private $modelName='';
	private $city='';
	private $buildingType='';
	private $numFloors='';
	private $roof='';
	private $wall='';
	private $area='';
	private $wwr='';
	
	// output results
	private $electricity=array( "Heating"=>0,
								"Cooling"=>0,
								"InteriorLighting"=>0,
								"ExteriorLighting"=>0,
						        "InteriorEquipment"=>0,
								"ExteriorEquipment"=>0,
								"Fans"=>0,
								"Pumps"=>0,
								"HeatRejection"=>0,
								"WaterSystems"=>0,
								"Refrigeration"=>0);
								
	private $naturalGas=array( "Heating"=>0,
        						"Cooling"=>0,
        						"InteriorLighting"=>0,
        						"ExteriorLighting"=>0,
        						"InteriorEquipment"=>0,
        						"ExteriorEquipment"=>0,
        						"Fans"=>0,
        						"Pumps"=>0,
        						"HeatRejection"=>0,
        						"WaterSystems"=>0,
        						"Refrigeration"=>0);
						
	private $totalSiteEnergy=0;
	private $totalSourceEnergy=0;
	private $naturalGasTotalEndUses=0;
	private $electricityTotalEndUses=0;
	
	// Default constructor
	public function __construct() {}
	
///////////////// Setter Methods //////////////////////////
	public function setUser($user) {
		$this->user=$user;
 	}  

	public function setBuilding($building) {
		$this->building = $building;
	}

	public function setCity($city)	{
		$this->city=$city;
	}

	public function setBuildingType($buildingType) {
		$this->buidlingType=$buildingType;
 	}  

	public function setNumFloors($numFloors) {
		$this->numFloors = $numFloors;
	}

	public function setRoof($roof)	{
		$this->roof=$roof;
	}

	public function setWall($wall) {
		$this->wall=$wall;
 	}  

	public function setArea($area) {
		$this->area = $area;
	}

	public function setWWR($wwr)	{
		$this->wwr=$wwr;
	}
	
	public function setUserID($userID) {
		$this->userID = $userID;
	}
	
	public function setModelName($modelName) {
		$this->modelName = $modelName;
	}
	
    ///////////////////////////// Running Process //////////////////////////	
	
	// Run Energy Simulation
	// Calling the VirtualPULSE_run.rb 
	// Pass 5 Arguments for the simulation
	public function runSimulation() {
		echo '<p>New building input file started. <p/>';
		$rubyCmdCreateIDF = 'xvfb-run -a ruby1.8 VirtualPULSE_run.rb '.
							$this->modelName.' '.						# ARGV[0] = idf_name
							$this->area.' '.							# ARGV[1] = area
							$this->numFloors.' '.	        			# ARGV[2] = num_floors
							$this->wwr.' '.								# ARGV[3] = wwr
							$this->city;                    			# ARGV[4] = city
		echo $rubyCmdCreateIDF;                    
		echo shell_exec($rubyCmdCreateIDF);
		echo "<p>Building Input File: ". $this->modelName. ".idf [created successfully]<p/>";
	}

	///////////////////////////////////////   Output Result   //////////////////////////
	// Display Basic Info of the Building Model
	public function displayBasicInfo() {
	
	}
	
	
	// All Graphic should be called after finished running simulation
	public function displayElectricity() {
	  
		$htmlFile = $this->modelName.'Table.html';
        $data= readHtml($this->FilePath,$htmlFile,"End Uses",2,7);
		
        $this->electricity["Heating"]=$data['Heating(GJ)'];
		$this->electricity["Cooling"]=$data['Cooling(GJ)'];              
		$this->electricity["InteriorLighting"]=$data['Interior Lighting(GJ)'];
		$this->electricity["ExteriorLighting"]=$data['Exterior Lighting(GJ)'];
		$this->electricity["InteriorEquipment"]=$data['Interior Equipment(GJ)'];
		$this->electricity["ExteriorEquipment"]=$data['Exterior Equipment(GJ)'];
		$this->electricity["Fans"]=$data['Fans(GJ)'];
		$this->electricity["Pumps"]=$data['Pumps(GJ)'];
		$this->electricity["HeatRejection"]=$data['Heat Rejection(GJ)'];
		$this->electricity["WaterSystems"]=$data['Water Systems(GJ)'];
		$this->electricity["Refrigeration"]=$data['Refrigeration(GJ)'];

		$graph = new Graph();
		$result = $graph->pieChart($this->electricity, "Electricity");
		
		print $result;
	}
	
	public function displayNaturalGas() {
	
		$htmlFile = $this->modelName.'Table.html';
		$data= readHtml($this->FilePath,$htmlFile,"End Uses",3,7);
        
		$this->naturalGas["Heating"]=$data['Heating(GJ)'];
		$this->naturalGas["Cooling"]=$data['Cooling(GJ)'];              
		$this->naturalGas["InteriorLighting"]=$data['Interior Lighting(GJ)'];
		$this->naturalGas["ExteriorLighting"]=$data['Exterior Lighting(GJ)'];
		$this->naturalGas["InteriorEquipment"]=$data['Interior Equipment(GJ)'];
		$this->naturalGas["ExteriorEquipment"]=$data['Exterior Equipment(GJ)'];
		$this->naturalGas["Fans"]=$data['Fans(GJ)'];
		$this->naturalGas["Pumps"]=$data['Pumps(GJ)'];
		$this->naturalGas["HeatRejection"]=$data['Heat Rejection(GJ)'];
		$this->naturalGas["WaterSystems"]=$data['Water Systems(GJ)'];
		$this->naturalGas["Refrigeration"]=$data['Refrigeration(GJ)'];

		$graph = new Graph();
		$result = $graph->pieChart($this->naturalGas, "Natural_Gas");
		
		print $result;
	}
	
	public function displaySummary() {
		
		$sqlFile = $this->modelName.'.sql';
		$getSql=$this->rubyRun.' '.$this->getDataRuby.' '.
		$this->FilePath.$sqlFile;
		echo $getSql;
		
		// calling the ruby to get the requested data from sql
		// get data from sql in ruby (This part is possible to be optimized)
		$totalSiteEnergy=floatval(shell_exec($getSql.' totalSiteEnergy'));
		$totalSourceEnergy=floatval(shell_exec($getSql.' totalSourceEnergy'));
		$naturalGasTotalEndUses=floatval(shell_exec($getSql.' naturalGasTotalEndUses'));
		$electricityTotalEndUses=floatval(shell_exec($getSql.' electricityTotalEndUses'));
		
		// Setting required data for the graph
		$this->totalSiteEnergy=$totalSiteEnergy;
		$this->totalSourceEnergy=$totalSourceEnergy;
		$this->naturalGasTotalEndUses=$naturalGasTotalEndUses;
		$this->electricityTotalEndUses=$electricityTotalEndUses;
		
		// Get Result
		$graph = new Graph();
		$result = $graph->barChart( $this->totalSiteEnergy,
									$this->totalSourceEnergy,
									$this->naturalGasTotalEndUses,
									$this->electricityTotalEndUses);
		print $result;
	}
    
    public function displayMonthlyData($selectedZone) {
        $htmlFile = $this->modelName.'Table.html';
        // Test RetrieveMonthlyData
        $data= retriveMonthlyData($this->FilePath, $htmlFile,"title",3,5);
        
        // $data[$key.$zone.$selectColumn]
        //echo '<h1> Monthly Cooling Report: Zone '.$selectedZone.', SYSSENSIBLECOOLINGENERGY[J]</h1><br>';
        
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
        $zone = $selectedZone;
        for($i=0; $i<12; $i++)
        {  
           $dataSet[$i] = $data[$month[$i].'Zone'.$zone.'Column3']; 
       //    print 'zone: '.$zone.', '.$month[$i].'  >>  '.$data[$month[$i].'Zone'.$zone.'Column3']." [J]<br>";
        }
        
        $graph = new GRAPH();
        $result=$graph->pieMonthlyChart($dataSet);
        print $result;
    }
    
    public function displayZoneMonthlyComparision($zone1, $zone2) {
        $htmlFile = $this->modelName.'Table.html';
        // Test RetrieveMonthlyData
        $data= retriveMonthlyData($this->FilePath, $htmlFile,"title",3,5);
        
        // $data[$key.$zone.$selectColumn]
       // echo '<h1>Monthly Cooling Report: Comparision between Zone '.$zone1.' and zone'.$zone2.', SYSSENSIBLECOOLINGENERGY[J]</h1><br><br>';
        
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
        for($i=0; $i<12; $i++)
        {  
           $dataSet1[$i] = $data[$month[$i].'Zone'.$zone1.'Column3']; 
           $dataSet2[$i] = $data[$month[$i].'Zone'.$zone2.'Column3']; 
          // print 'zone: '.$zone1.', '.$month[$i].'  >>  '.$data[$month[$i].'Zone'.$zone1.'Column3']." [J]<br>";
          // print 'zone: '.$zone2.', '.$month[$i].'  >>  '.$data[$month[$i].'Zone'.$zone2.'Column3']." [J]<br><br>";
        }
        
        $graph = new GRAPH();
        $result=$graph->barMonthlyZoneChart($dataSet1, $dataSet2, $zone1, $zone2);
        print $result;
    }
    
    public function refresh() {
        $graph = new GRAPH();
        print $graph->refresh;
    }
}


?>
