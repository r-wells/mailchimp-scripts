<?php


function parse_csv_file($file){
  $data = explode("\n",file_get_contents($file));
  $out = [];
  $i = 0;
  
  
  
  
  foreach($data as $row){
	  
	
	  
	  
    $row = explode("|",$row);
	
	//  print_r($row);
	
	
    if(strpos($row[10],'@') !=false){
      $out[$row[0]]['full_name'] = $row[1];
      $out[$row[0]]['fname'] = $row[2];
      $out[$row[0]]['lname'] = $row[3];
      $out[$row[0]]['address1'] = $row[4];
      $out[$row[0]]['address2'] = $row[5];
      $out[$row[0]]['city'] = $row[6];
      $out[$row[0]]['state'] = $row[7];
      $out[$row[0]]['zip'] = $row[8];
      $out[$row[0]]['email'] = $row[10];
	//   $out[$row[0]]['last_order_date'] = $row[16];
	// //  $out[$row[0]]['last_order_amount'] = $row[17];
	//   $out[$row[0]]['life_to_date_sales'] = $row[18];
	//   $out[$row[0]]['life_to_date_orders'] = $row[19];
	  
      $i++;
      if(DEBUG && $i==6){
        break;
      }
    }
  }
  
  
  
  
  return $out;

}

function most_recent_file($folder) {
  $dircontent = scandir($folder);
  $arr = array();
  foreach($dircontent as $filename) {
    if ($filename != '.' && $filename != '..' && $filename != '.ftpquota') {
      $dat =  filemtime($folder.'/'.$filename);
      $arr[$dat] = $filename;
    }
  }
  if (!krsort($arr)) return false;
  foreach($arr as $most_recent){
    return $most_recent;
  }

}

//Ryan's added functions

// parse through csv file and return $methods array
function get_csv_contents($filelocation) {
  $row = 1;
    $methods = [];
    if (($handle = fopen($filelocation, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=1; $c < $num; $c++) {
            $methods[$row] = $data;
        }
        $row++;
      }
      fclose($handle);
    }

    return $methods;
}

//splits first & last name, adds email to array instance
//returns $newrecord array
function split_recipient_name($methods) {

  $newrecord = [];
  for($i = 1; $i <= sizeof($methods); $i++) {
      $keys = ["FirstName", "LastName", "Email"];
      $record = explode(" ", $methods[$i][3]);

      array_push($record, $methods[$i][9]);

      if(sizeof($record) > 3) {
          array_splice($record, 1, 1);
      } elseif (sizeof($record) < 3) {
          $record[2] = $record[1];
          $record[1] = "No Last Name";
      }

      $record["FirstName"] = $record[0];
      unset($record[0]);
      $record["LastName"] = $record[1];
      unset($record[1]);
      $record["Email"] = $record[2];
      unset($record[2]);

      array_push($newrecord, $record);
      
  }

  return $newrecord;
}

function return_info ($methods) {
  $newrecord = [];
  $finalrecord = [];
  $d1 = date('Y-m-d', strtotime("-5 days"));
  for($i = 1; $i < sizeof($methods) - 1; $i++) {
      $keys = ["Email", "FirstName", "LastName"];
      $newrecord[$i][0] = $methods[$i][1];
      $newrecord[$i][1] = $methods[$i][2];
      $newrecord[$i][2] = $methods[$i][3];
      $newrecord[$i][3] = explode(" ", $methods[$i][6]);
      $newrecord[$i][3] = trim($newrecord[$i][3][0]);
      // $newrecord[$i][4] = $d1;

      $newrecord[$i]["Email"] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $newrecord[$i][0]);
      unset($newrecord[$i][0]);
      $newrecord[$i]["FirstName"] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $newrecord[$i][1]);
      unset($newrecord[$i][1]);
      $newrecord[$i]["LastName"] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $newrecord[$i][2]);
      unset($newrecord[$i][2]);

      $newrecord[$i][3] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $newrecord[$i][3]);

      if($newrecord[$i][3] !== $d1) {
        unset($newrecord[$i]);
      }
  }
  
  return $newrecord;
}
 