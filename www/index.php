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
        $callStartTime = microtime(true);
        $zoom = explode('&', explode('=', $_POST['IntelUrl'])[2])[0];
        $list = explode('_', explode('=', $_POST['IntelUrl'])[3]);
        $loop=1;
        $list_data[0] = array('ordre', 'gps_source', 'gps_destination', 'source', 'destination', 'intel_url', 'gmap_source', 'gmap_destination');
        foreach($list as $link){
                if($loop < 10)
                        $ordre = 'lien 0'.$loop;
                else
                        $ordre = 'lien '.$loop;

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
                $list_data[$loop] = array($ordre, $gps_source, $gps_destination, 'A Remplir',  'A Remplir',  $intel_url, $gmap_source, $gmap_destination);
                $loop++;
        }
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        echo '<p>'.date('H:i:s').' Liste des liens genere en '.sprintf('%.4f',$callTime).' secondes<br>';
		$nblink=count($list_data)-1;
		echo date('H:i:s').' '.$nblink.' liens</p>';
        return $list_data;
}
function gen_list_key($list){
	$callStartTime = microtime(true);
	$loop=0;
	foreach($list as $link){
		if($loop >= 1){
			$list_dest[$loop - 1]=$link[2];
		}
		$loop++;
	}
	$list_key = array_count_values($list_dest);
	$callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;
    echo '<p>'.date('H:i:s').' Liste des key genere en '.sprintf('%.4f',$callTime).' secondes<br>';
	$nbdest=count($list_key);
	echo date('H:i:s').' '.$nbdest.' destinations<br>';
	$nbkey=array_sum($list_key);
	echo date('H:i:s').' '.$nbkey.' cle</p>';
	return $list_key;
}
function gen_list_table($list){
	$callStartTime = microtime(true);
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
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        echo date('H:i:s').' Tableau genere en '.sprintf('%.4f',$callTime).' secondes</p>';
        return $table;
}
function gen_key_table($list){
	$callStartTime = microtime(true);
	$table = '<table border="1"><tr><th>Portail Destination</th><th>Nb cle</th><th>Intel Url</th></tr>';
	foreach ($list as $clef => $valeur) {
        $table .= '<tr><td>'.$clef.'</td><td>'.$valeur.'</td><td><a href="http://www.ingress.com/intel?pll='.$clef.'"> Url ici</a></td></tr>';
    }
	$table .= '</table>';
    $callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;
    echo date('H:i:s').' Tableau genere en '.sprintf('%.4f',$callTime).' secondes</p>';
    return $table;
}
function gen_xls($list_link, $list_key, $name = NULL) {
	echo '<p>'.date('H:i:s').' Debut de la generation des xls<br>';
	if( ! $name){
		$name = md5(uniqid() . microtime(TRUE) . mt_rand());
	}else{
        	$name= md5($name);
    }
	echo date('H:i:s').' Nom du fichier: <b>'.$name.'</b><br>';
	date_default_timezone_set('Europe/Paris');
	require_once('Classes/PHPExcel.php');
	// Create new PHPExcel object
	echo date('H:i:s'), ' Creation de l\'objet PHPExcel<br>';
	$callStartTime = microtime(true);
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
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo date('H:i:s').' Ajout des donnees en '.sprintf('%.4f',$callTime).' secondes</p>';

	// Save Excel 2007 file
	$callStartTime = microtime(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('xlsx/'.$name.'.xlsx');
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo '<p>'.date('H:i:s').' Sauvegarde au format Excel 2007 en '.sprintf('%.4f',$callTime).' secondes<br>';
	echo date('H:i:s').' Fichier disponible <a href="xlsx/'.$name.'.xlsx">ici</a></p>';

	// Save Excel 95 file
	$callStartTime = microtime(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('xls/'.$name.'.xls');
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo '<p>'.date('H:i:s').' Sauvegarde au format Excel 95 en '.sprintf('%.4f',$callTime).' secondes<br>';
	echo date('H:i:s').' Fichier disponible <a href="xls/'.$name.'.xls">ici</a></p>';
}
function drive_push(){
	$callStartTime = microtime(true);
	exec('cd /home/artnod/gdrive && ../gopath/bin/drive push -convert -quiet');
	$callEndTime = microtime(true);
    $callTime = $callEndTime - $callStartTime;
    echo '<p>'.date('H:i:s').' Drive push en '.sprintf('%.4f',$callTime).' secondes</p>';
}
?>
<h1><a href="./">Links List</a></h1>
<?php
	if (test_url($_POST['IntelUrl'])){

		echo '<p>'.date('H:i:s').' Url ok<br>';
		echo date('H:i:s').'<a href="'.$_POST['IntelUrl'].'"> Url saisi</a></p>';

		$list_link = gen_list_link($_POST['IntelUrl']);
		$list_link = gen_list_key($list_link);
		
		echo '<p>'.gen_list_table($list_link).'</p>';
		
		echo '<p>'.gen_key_table($list_key).'</p>';
		
		gen_xls($list_link, $list_key, $_POST['IntelUrl']);

		drive_push();
	}else{
		echo '<form action="./" method="post">
			<p>
			<input type="text" name="IntelUrl" />
			<input type="submit" value="Valider" />
			</p>
			</form>';
	}
?>
<p>v0.2</p>
