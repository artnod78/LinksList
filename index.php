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
	$zoom = explode('&', explode('=', $_POST['IntelUrl'])[2])[0];
	$list = explode('_', explode('=', $_POST['IntelUrl'])[3]);
	$loop=1;
	$list_data = array();
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
		$intel_url = 'https://www.ingress.com/intel?ll='.$moy_x.','.$moy_y.'&z='.$zoom.'&pls='.$link.'<br>';
		$gmap_source = 'https://www.google.fr/maps/search/'.$gps_source;
		$gmap_destination = 'https://www.google.fr/maps/search/'.$gps_destination;
		$link_data = array (
			'Ordre' => $ordre,
			'GpsSource' => $gps_source,
			'GpsDestination' => $gps_destination,
			'NomSource' => 'A Remplir',
			'NomDestination' => 'A Remplir',
			'IntelUrl' => $intel_url,
			'GmapSource' => $gmap_source,
			'GmapDestination' => $gmap_destination
		);
		$list_data[$loop-1] = $link_data;
		$loop++;
	}
	return $list_data;
}
function gen_table($list){
	$starter = 'http';
	$table = '<table border="1">';
	$table .= '<tr>';
	foreach(array_keys($list[0]) as $key){
		$table .= '<th>'.$key.'</th>';
	}
	$table .= '</tr>';
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
	return $table;
}
function gen_csv($list) {
	$new_list[] = array_keys($list[0]);
	foreach($list as $link){
		$new_list[] = $link;
	}
	$fp = fopen('file.csv', 'w');
	foreach ($new_list as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
}
?>
<h1>Links List</h1>

<form action="index.php" method="post">
	<p>
		<input type="text" name="IntelUrl" />
		<input type="submit" value="Valider" />
	</p>
</form>

<?php
	if (test_url($_POST['IntelUrl'])){
		echo '<p>url ok<br>';
		$list = gen_list($_POST['IntelUrl']);
		echo count($list).' liens</p>';
		echo gen_table($list);
		gen_csv($list);
		echo '<a href="file.csv">csv ici</a>';
	}
?>

<p>v0.1</p>