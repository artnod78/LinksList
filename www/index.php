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
			$list_temp=[$loop - 1]=$link[2];
		}
		$loop++;
	}
	$list_key = array_count_values($list_temp);

}
?>
<h1><a href="./">Links List</a></h1>
<?php
	if (test_url($_POST['IntelUrl'])){
		echo '<p>'.date('H:i:s').' Url ok<br>';
		echo date('H:i:s').'<a href="'.$_POST['IntelUrl'].'"> Url saisi</a></p>';
		$list_link = gen_list_link($_POST['IntelUrl']);
		$list_key = gen_list_key($list_link);
	}
	else{
		echo '<form action="./" method="post">
			<p>
			<input type="text" name="IntelUrl" />
			<input type="submit" value="Valider" />
			</p>
			</form>';
	}
?>
<p>v0.2</p>
