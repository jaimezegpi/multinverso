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




if ( count($server_population_clients)<1 ){
	$t=time();
	$server_population = fopen( "server_population.dat", "w+" );
	fwrite($server_population, $_SESSION['player_id'].";".date("Y-m-d",$t).";".$user_data);
	fclose( $server_population );
	exit();
}elseif( count($server_population_clients)>=1 ){
	$t=time();
	$return_data = "";
	$user_detector = false;
	foreach ( $server_population_clients as $key => $client ) {
		echo "X";
		$client_data = explode(";", $client);
		if ( $key>0 ){
			$return_data.="|";
		}

		if ($_SESSION['player_id'] == $client_data[0]){
			$user_detector = true;
			$return_data.=$_SESSION['player_id'].";".date("Y-m-d",$t).";".$user_data;
		}else{
			$return_data.=$client;
		}

	}
	if (!$user_detector){
		$return_data.="|".$_SESSION['player_id'].";".date("Y-m-d",$t).";".$user_data;
	}
	$server_population = fopen( "server_population.dat", "w+" );
	fwrite($server_population, $return_data);
	fclose( $server_population );

}else{
	exit();
}

exit();