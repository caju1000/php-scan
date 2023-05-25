<?php 
echo " ____  __ __  ____        _____   __   ____  ____  
|    \|  |  ||    \      / ___/  /  ] /    ||    \ 
|  o  )  |  ||  o  )    (   \_  /  / |  o  ||  _  |
|   _/|  _  ||   _/      \__  |/  /  |     ||  |  |
|  |  |  |  ||  |        /  \ /   \_ |  _  ||  |  |
|  |  |  |  ||  |        \    \     ||  |  ||  |  |
|__|  |__|__||__|         \___|\____||__|__||__|__|1.1
                                                   \n by CaJu\n\n";

if ( empty($argv[1]) || $argv[1]=="-h"){
  $_cmd = "\n ===== HELP ===== \n";
  $_cmd .= "php varrer.php <IP> \n\n";
  $_cmd .= "EXAMPLE: \n";
  $_cmd .= "#Scaning a host \n";
  $_cmd .= "php varrer.php 192.168.10.10 \n\n";
  $_cmd .= "#Scaning a network \n";
  $_cmd .= "php varrer.php 192.168.10.0 \n\n";
  exit($_cmd);
}

// Parameter IP
$parametro = $argv[1];
// Separates the octets
list($pri, $seg, $ter, $qua) = explode(".",$parametro);
$rede =  $pri.".".$seg.".".$ter; 

/*
 * PORTS *
 * 19 - CHANGEN
 * 17 - QOTD
 * 23 - TELNET
 * 67,68 - DHCP
 * 69 - TFTP
 * 111 - PORTMAP
 * 123 - NTP
 * 135,136,137,138,139 - MICROSOFT NetBIOS TCP/UDP
 * 1900 - SSDP UDP
 * 3389 - RDP
 * 5353 - DNS
 * 5900 - VNC
 * 1433 - MS-SQL
* */

$port_array = array(17,19,21,22,23,25,53,67,68,69,80,110,111,123,135,136,137,138,139,143,443,445,993,995,1433,1723,1900,3306,3389,5353,5900,8080);

// Creates a string with all ports separated by a comma
$portas = "";
foreach($port_array as $ports){
$portas .= $ports.",";}
// Remove the last comma
$portas = substr($portas, 0, -2);

// fourth octet
if ($qua == 0)
{
echo "\n CHECKING HOSTS ALIVE...\n";
// Creating file with active hosts
system("fping -a -g -q $rede.0/24 | tee hosts.txt");
$file = fopen("hosts.txt","r") or die("Problema no arquivo hosts!");

	while(! feof($file))
	{	
  //Get IP each line of file  
	$line_ip = fgets($file);
    // Jump IP 1    
    if (trim($line_ip) == trim("$rede.1")){
      continue;
    }

	$line_ip = trim($line_ip);
	echo "\n\n CHECKING HOST $line_ip";
	echo "\n---------------------------\n";
	
  foreach ($port_array as $service_port)
  {
    // Create a TCP/IP socket.   
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket === false) {
      echo "Failed Creating socket $parametro...\n";
    } 
    // Try connect    
    $result = @socket_connect($socket, $parametro, $service_port);
    if ($result === false) {
      continue;
    } else {
      // Get the name of services
      $cmd = "cat /etc/services | grep -iw '$service_port' | cut -f 1";	    
      echo "Port $service_port...OK - ";
      echo shell_exec($cmd);
    }
    // Closing socket;
    socket_close($socket);    
  }
  
	echo "\n FINISHED. \n";
	echo "============================== \n\n";
	}

fclose($file);
}else{
	echo "\n CHECKING HOST $parametro \n";
    echo "----------------------------- \n";
    
    foreach ($port_array as $service_port)
    {
      // Create a TCP/IP socket.   
      $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
      if ($socket === false) {
        echo "Failed Creating socket $parametro...\n";
      } 
      // Try connect      
      $result = @socket_connect($socket, $parametro, $service_port);
      if ($result === false) {
        continue;
      } else {
        // Get the name of services
        $cmd = "cat /etc/services | grep -iw '$service_port' | cut -f 1";	      
        echo "Port $service_port...OK - ";        
        echo shell_exec($cmd);
      }
      // Closing socket
      socket_close($socket);      
    }


    echo "\n FINISHED. \n";
    echo "================ \n\n";
}




?>

