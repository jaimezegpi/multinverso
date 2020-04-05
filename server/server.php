<?php 
/*SERVER*/
if( !isset($_GET["action"]) ){ exit(); }

if ( !session_id() ){session_start();}

if ( !file_exists("map.txt") ){
	$fp = fopen('map.txt', 'w');
	fclose($fp);
}

if ( !file_exists("map_users.txt") ){
	$fp = fopen('map_users.txt', 'w');
	fclose($fp);
}

switch ($_GET["action"]) {
	case 'get_id':
		echo '{"id":"'.session_id().'"}';
		break;

	case 'get_map_users':
		$map_users = file_get_contents("map_users.txt");
		echo $map_users;
		break;


	case 'update_user':
		deleteInactiveUsers();
		$current_timestamp = microtime(true);
		if ( !isset($_GET["position"]) || !isset($_GET["rotation"]) ){ exit('Error de esquema'); }
		$map_users = json_decode ( file_get_contents("map_users.txt") );
		if (!$map_users){ $map_users = array(); }
		$user_exist = false;
		foreach ($map_users as $key => $user) {
			if ($map_users){
				$time_inactive = $current_timestamp-$user->t;
			}else{
				$time_inactive = 0;
			}

			//if ($time_inactive>10){ echo "Deleting for inactivity ".$map_users[$key]->u;  continue; }
			
			if ($user->u == session_id()){
				$map_users[$key]->p = $_GET["position"]; 
				$map_users[$key]->r = $_GET["rotation"]; 
				$map_users[$key]->t = $current_timestamp;
				$user_exist = true;
			}
		}
		if (!$user_exist){
			echo "!!!!!!!! creo usuario";
			array_push($map_users, array( "u"=>session_id(),"p"=>$_GET["position"],"r"=>$_GET["rotation"],"t"=>$current_timestamp ) );
		}

		$fp = fopen('map_users.txt', 'w');
		fwrite($fp, json_encode($map_users) );
		fclose($fp);
		break;

	case 'delete_inactive_users':
		deleteInactiveUsers();
	break;

	default:
		# code...
		break;
}

function deleteInactiveUsers(){
	$current_timestamp = microtime(true);
	$map_users = json_decode ( file_get_contents("map_users.txt") );
	$delete_keys = array();
	foreach ($map_users as $key => $user) {
		if ($map_users){
			$time_inactive = $current_timestamp-$user->t;
		}else{
			$time_inactive = 0;
		}
		if ($time_inactive>10){
			array_push($delete_keys, $key);
		}
	}
	foreach ($delete_keys as $key => $value) {
		 unset($map_users[$value]);
	}
	 $map_users = array_values($map_users);
	$fp = fopen('map_users.txt', 'w');
	fwrite($fp, json_encode($map_users) );
	fclose($fp);
	
}