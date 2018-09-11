<?php
session_start();
/*
session
time
0 position
1 rotation
*/
/*------------USER DATA----------- INI*/
if ( isset( $_GET['user_data'] ) ){
	$user_data = $_GET['user_data'];
	$user_data_array = explode(";", $_GET['user_data']);

	if ( count($user_data_array)<1 ){ exit(1); }
}else{ exit(2); }
/*------------USER DATA----------- END*/

if( !isset( $_SESSION['player_id'] ) ){
	$_SESSION['player_id'] = session_id();
}


if ( !file_exists('server_population.dat') ){
	$server_population = fopen( "server_population.dat", "w" );
	fclose( $server_population );
	$server_population = file_get_contents( "server_population.dat" );
}else{
	$server_population = file_get_contents( "server_population.dat" );
}

$server_population_clients = explode("|", $server_population);

if ( count($server_population_clients)<1 || $server_population=='' ){
	$t = strtotime( Date('Y-m-d H:i:s') );
	$server_population = fopen( "server_population.dat", "w+" );
	fwrite($server_population, $_SESSION['player_id'].";".$t.";".$user_data);
	fclose( $server_population );
	exit();
}elseif( count($server_population_clients)>=1 ){
	$t = strtotime( Date('Y-m-d H:i:s') );
	$return_data = "";
	$user_detector = false;
	foreach ( $server_population_clients as $key => $client ) {
		$client_data = explode(";", $client);

		if ( isset( $client_data[0] ) ){ $client_data_player_id = $client_data[0]; }else{
			$client_data_player_id = ''; continue;
		}

		if ( isset( $client_data[1] ) ){ $client_data_player_time = $client_data[1]; }else{
			$client_data_player_time = '';
			continue;
		}

		if (isset($client_data_player_time)){
			$client_pause_time = $t-$client_data_player_time;
			if ($client_pause_time>50){ continue; }
		}else{ continue; }

		if ( $key>0 ){
			$return_data.="|";
		}

		if ($_SESSION['player_id'] == $client_data_player_id){
			$user_detector = true;
			$return_data.=$_SESSION['player_id'].";".$t.";".$user_data;
		}else{
			$return_data.=$client;
		}

	}
	if (!$user_detector && isset($client_data_player_time) ){
		if ($return_data){
			$return_data.="|".$_SESSION['player_id'].";".$t.";".$user_data;
		}else{
			$return_data.=$_SESSION['player_id'].";".$t.";".$user_data;
		}
		
	}
	$server_population = fopen( "server_population.dat", "w+" );
	fwrite($server_population, $return_data);
	fclose( $server_population );

}else{
	exit();
}

echo $_SESSION['player_id'];
exit();