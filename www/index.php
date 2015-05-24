<?php
function test_url($url){
        $starter = 'https://www.ingress.com/intel?ll=';
        $splitter = '_';
        if (substr($url, 0, strlen($starter)) === $starter && strpos($url, $splitter) !== FALSE){
                return true;
        }
        else{
                return false;
        }
}

function gen_list_link($url){
    $zoom = explode('&', explode('=', $_POST['IntelUrl'])[2])[0];
    $list = explode('_', explode('=', $_POST['IntelUrl'])[3]);
    $loop=1;
    $list_data[0] = array('ordre', 'gps_source', 'gps_destination', 'source', 'destination', 'intel_url', 'gmap_source', 'gmap_destination');
    foreach($list as $link){
        if($loop < 10){
            $ordre = 'lien 0'.$loop;
		}
        else{
            $ordre = 'lien '.$loop;
		}
        $src_x = explode(',', $link)[0];
        $src_y = explode(',', $link)[1];
        $dest_x = explode(',', $link)[2];
        $dest_y = explode(',', $link)[3];
        $gps_source = $src_x.','.$src_y;
        $gps_destination = $dest_x.','.$dest_y;
        $moy_x=($src_x+$dest_x)/2;
        $moy_y=($src_y+$dest_y)/2;
        $intel_url = 'https://www.ingress.com/intel?ll='.$moy_x.','.$moy_y.'&z='.$zoom.'&pls='.$link;
        $gmap_source = 'https://www.google.fr/maps/search/'.$gps_source;
        $gmap_destination = 'https://www.google.fr/maps/search/'.$gps_destination;
        $list_data[$loop] = array($ordre, $gps_source, $gps_destination, '__A Remplir__',  '__A Remplir__',  $intel_url, $gmap_source, $gmap_destination);
        $loop++;
    }
	$nblink=count($list_data)-1;
	echo '<p>'.$nblink.' liens<br>';
    return $list_data;
}

function gen_list_key($list){
	$loop=0;
	foreach($list as $link){
		if($loop >= 1){
			$list_temp[$loop - 1]=$link[2];
		}
		$loop++;
	}
	$list_key = array_count_values($list_temp);
	$list_dest = array_keys($list_key);
	$list_nbkey = array_values($list_key);
	$nblink=count($list_key);
	$nbkey=array_sum($list_key);
	$list_data[0]= array('destination','nb cle','intel url');
	$loop=0;
	while($loop < $nblink){
		$list_data[$loop + 1] = array($list_dest[$loop],$list_nbkey[$loop],'https://www.ingress.com/intel?pll='.$list_dest[$loop]);
		$loop++;
	}
	echo $nblink.' destinations</p>';
	return $list_data;
}

function gen_table($list){
    $starter = 'http';
    $table = '<table border="1">';
    $loop=0;
	foreach($list as $link){
        if($loop == 0 ){
			$table .= '<tr>';
        foreach($link as $value){
            $table .= '<th>'.$value.'</th>';
        }
                	$table .= '</tr>';
		}
		else{
			$table .= '<tr>';
                	foreach($link as $value){
                	        if(substr($value, 0, strlen($starter)) === $starter){
                	                $table .= '<td><a href="'.$value.'">url ici</a></td>';
				}
				else{
                	                $table .= '<td>'.$value.'</td>';
				}
                	}
                	$table .= '</tr>';
                }
		$loop++;
        }
        $table .= '</table>';
        return $table;
}

function gen_xls($list_link, $list_key, $name = NULL) {
	if( ! $name){
		$name = md5(uniqid() . microtime(TRUE) . mt_rand());
	}else{
        	$name= md5($name);
    }
	echo '<p>Nom du fichier: <b>'.$name.'</b></p>';
	date_default_timezone_set('Europe/Paris');
	require_once('Classes/PHPExcel.php');
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();	
	// Create a first sheet, representing sales data
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->fromArray($list_link, null, 'A1');
	$objPHPExcel->getActiveSheet()->setTitle('Links List');
	// Create a new worksheet, after the default sheet
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->fromArray($list_key, null, 'A1');
	$objPHPExcel->getActiveSheet()->setTitle('Key List');
	// Save Excel 2007 file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('xlsx/'.$name.'.xlsx');
	echo '<p>Sauvegarde au format Excel 2007<br>';
	echo 'Fichier disponible <a href="xlsx/'.$name.'.xlsx">ici</a></p>';
	// Save Excel 95 file
	$callStartTime = microtime(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('xls/'.$name.'.xls');
	echo '<p>Sauvegarde au format Excel 95<br>';
	echo 'Fichier disponible <a href="xls/'.$name.'.xls">ici</a></p>';
}

function drive_push(){
	$callStartTime = microtime(true);
	exec('cd /home/artnod/gdrive && ../gopath/bin/drive push -convert -quiet');
	$callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;
    echo '<p>Drive push en '.sprintf('%.4f',$callTime).' secondes</p>';
}
?>
<h1><a href="./">Links List</a></h1>
<?php
	if (test_url($_POST['IntelUrl'])){
		echo '<p>Url ok<br>';
		echo '<a href="'.$_POST['IntelUrl'].'">Url saisi</a></p>';
		
		$list_link = gen_list_link($url);
		$list_key=gen_list_key($list_link);
		
		echo '<p>'.gen_table($list_link).'</p>';
		echo '<p>'.gen_table($list_key).'</p>';
		
		gen_xls($list_link, $list_key, $_POST['IntelUrl']);
		
		//drive_push();
	}
	else{
		echo '<form action="./" method="post">
			<p>
			<input type="text" name="IntelUrl" value="https://www.ingress.com/intel?ll=48.853559,2.348869&z=19&pls=48.853356,2.348783,48.853624,2.349053_48.853356,2.348783,48.853761,2.348867_48.853761,2.348867,48.853624,2.349053"/>
			<input type="submit" value="Valider" /><br>
			</p>
			</form>';
	}
?>
<p>v0.4</p>
