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
function gen_list($url){
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
        echo date('H:i:s').' Liste des liens genere en '.sprintf('%.4f',$callTime).' secondes<br>';
        return $list_data;
}
function gen_table($list){
	$callStartTime = microtime(true);
        $starter = 'http';
        $table = '<table border="1">';
        foreach($list as $link){
                $table .= '<tr>';
                foreach($link as $value){
                        if(substr($value, 0, strlen($starter)) === $starter)
                                $table .= '<th><a href="'.$value.'">url ici</a></th>';
                        else
                                $table .= '<th>'.$value.'</th>';
                }
                $table .= '</tr>';
        }
        $table .= '</table>';
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        echo date('H:i:s').' Tableau genere en '.sprintf('%.4f',$callTime).' secondes</p>';
        return $table;
}
function gen_xls($list, $name = NULL) {
	echo '<p>'.date('H:i:s').' Debut de la generation des xls<br>';
	if( ! $name){
		$name = md5(uniqid() . microtime(TRUE) . mt_rand());
	}else{
        	$name= md5($name);
    	}
	date_default_timezone_set('Europe/Paris');
	require_once('Classes/PHPExcel.php');
	// Create new PHPExcel object
	echo date('H:i:s'), ' Creation de l\'objet PHPExcel<br>';
	$objPHPExcel = new PHPExcel();
	// Add  data
	$callStartTime = microtime(true);
	$objPHPExcel->getActiveSheet()->fromArray($list, null, 'A1');
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo date('H:i:s').' Ajout des donnees en '.sprintf('%.4f',$callTime).' secondes<br>';
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Links List');
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	// Save Excel 2007 file
	$callStartTime = microtime(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('xlsx/'.$name.'.xlsx');
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo date('H:i:s').' Sauvegarde au format Excel 2007 en '.sprintf('%.4f',$callTime).' secondes<br>';
	echo date('H:i:s').' Fichier disponible <a href="xlsx/'.$name.'.xlsx">ici</a><br>';
	// Save Excel 95 file
	$callStartTime = microtime(true);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('xls/'.$name.'.xls');
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo date('H:i:s').' Sauvegarde au format Excel 95 en '.sprintf('%.4f',$callTime).' secondes<br>';
	echo date('H:i:s').' Fichier disponible <a href="xls/'.$name.'.xls">ici</a><br>';
	// Fin
	echo date('H:i:s').' Fin.</p>';
}
function drive_push(){
	$callStartTime = microtime(true);
	exec('cd /home/artnod/gdrive && ../gopath/bin/drive push -quiet');
	$callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        echo date('H:i:s').' Drive push en '.sprintf('%.4f',$callTime).' secondes<br>';
}
?>
<h1><a href="./">Links List</a></h1>
<?php
	if (test_url($_POST['IntelUrl'])){
		echo '<p>'.date('H:i:s').' Url ok<br>';
		echo date('H:i:s').'<a href="'.$_POST['IntelUrl'].'"> Url saisi</a><br>';
		$list = gen_list($_POST['IntelUrl']);
		$nblink=count($list)-1;
		echo date('H:i:s').' '.$nblink.' liens<br>';
		echo '<p>'.gen_table($list).'</p>';
		gen_xls($list, $_POST['IntelUrl']);
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
<p>v0.1</p>
