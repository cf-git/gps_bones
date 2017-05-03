<?php 

//error_reporting(~E_NOTICE);

date_default_timezone_set('Asia/Yekaterinburg');
error_reporting(E_ALL & ~E_NOTICE); 
set_time_limit (0); 
$ip='0.0.0.0'; 
$port='5027'; 
include 'database.class.php';
$database = new wsilence\BeaconDatabase();
include 'vendor/autoload.php';




if(!($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) 
{ 
    $errorcode = socket_last_error(); 
    $errormsg = socket_strerror($errorcode); 
      
    die("Couldn't create socket: [$errorcode] $errormsg \n"); 
} 
  
echo "Socket created \n"; 
  
// Bind the source address 
if( !socket_bind($sock, $ip , $port) ) 
{ 
    $errorcode = socket_last_error(); 
    $errormsg = socket_strerror($errorcode); 
      
    die("Could not bind socket : [$errorcode] $errormsg \n"); 
} 
  
echo "Socket bind OK \n"; 

//listen the socket 
if(!socket_listen ($sock , 10)) 
{ 
    $errorcode = socket_last_error(); 
    $errormsg = socket_strerror($errorcode); 
      
    die("Could not listen on socket : [$errorcode] $errormsg \n"); 
} 
  
echo "Socket listen OK \n"; 


echo "Waiting for incoming connections... \n"; 
  
//array of client sockets 
$client_socks = array(); 
$max_clients=10;   
//array of sockets to read 
$read = array(); 
  
//start loop to listen for incoming connections and process existing connections 
while (true)  
{ 
    //prepare array of readable client sockets 
    $read = array(); 
      
    //first socket is the master socket 
    $read[0] = $sock; 
      
    //now add the existing client sockets 
    for ($i = 0; $i < $max_clients; $i++) 
    { 
        if($client_socks[$i] != null) 
        { 
            $read[$i+1] = $client_socks[$i]; 
        } 
    } 
      
    //now call select - blocking call 
    if(socket_select($read , $write , $except , null) === false) 
    { 
        $errorcode = socket_last_error(); 
        $errormsg = socket_strerror($errorcode); 
      
        die("Could not listen on socket : [$errorcode] $errormsg \n"); 
    } 
      
    //if ready contains the master socket, then a new connection has come in 
    if (in_array($sock, $read))  
    { 
        for ($i = 0; $i < $max_clients; $i++) 
        { 
            if ($client_socks[$i] == null)  
            { 
                $client_socks[$i] = socket_accept($sock); 
                  
                //display information about the client who is connected 
                if(socket_getpeername($client_socks[$i], $address, $port)) 
                { 
                    echo "Client $address : $port is now connected to us. \n"; 
                } 
                break; 
            } 
        } 
    } 
  
    //check each client if they send any data 
    for ($i = 0; $i < $max_clients; $i++) 
    { 
        if (in_array($client_socks[$i] , $read)) 
        { 
            $input = socket_read($client_socks[$i] , 10240, PHP_BINARY_READ); 
              
            if ($input == null || $input === '')  
            { 
                //zero length string meaning disconnected, remove and close the socket 
                unset($client_socks[$i]); 
                socket_close($client_socks[$i]); 
            } 
                        
            //echo "Sending output to client \n"; 
            //echo $output."\n"; 
            //echo "port:".$port."\n"; 
            //echo "client_sock=".$client_socks[$i]."\n"; 
            //send response to client 
            if(strlen($input) == 17)
            { 
                // Recieve data from tcp socket
                $payloadFromDevice = bin2hex($input);

                // Decode recieved data
                $decoder = new Uro\TeltonikaFmParser\TcpDecoder();

                // Check if data which we recieved are authentication or Gps records
                //if($decoder->isAuthentication($payloadFromDevice)){ // returns true;
                    
                    $imei = $decoder->decodeAuthentication($payloadFromDevice);
                    //echo json_encode($imei);
                    //print_r($imei->Imei);
                    $imei=json_encode($imei);
                    $imei=json_decode($imei);
                    //print_r($imei->imei);

                    // Check if device is authenticated in your system, and then encode response for device
                    $encoder = new Uro\TeltonikaFmParser\TcpEncoder();
                    $payload = $encoder->encodeAuthentication(true); // Yes, device was authenticated successfully
                    $res_write=socket_write($client_socks[$i], chr("01"));
                    // send $payload though the socket.
                //}  

                $input = socket_read($client_socks[$i] , 10240, PHP_BINARY_READ); 
                  
                if ($input == null || $input === '')  
                { 
                    //zero length string meaning disconnected, remove and close the socket 
                    unset($client_socks[$i]); 
                    socket_close($client_socks[$i]); 
                } 

                $input = bin2hex($input); // from gps module rawdata bin to hex

                // Now we need to wait for next data from the device

                // Recieve next payload from the socket (now with data)
                $tcpPayloadFromDevice = $input;

                // Decode it
                $decoder = new Uro\TeltonikaFmParser\TcpDecoder();
                $data = $decoder->decodeData($input);

                //echo json_encode($data);
                $data=json_encode($data);
                $data=json_decode($data);
                
                $res_write=socket_write($client_socks[$i], chr("00000002"));

                unset($client_socks[$i]);  
            }
            $output = "recived data=$input"."\n"."length=".strlen($input); 
            //echo "\n response=".$response."\n"; 

            //echo "\n ************************* \n"; 
            //print_r($data);
            if(is_array($data))
            {
                foreach($data as $row)
                {
                    switch ($database->checkIsCreated($imei->imei, $row->dateTime->date))
                    {
                        case 0:
                            $database->insertRow($imei, $row);
                            break;
                        case 1:
                            $database->updateRow($imei, $row);
                            break;
                    }
                }
            }   
        } 
    } 
} 

?>