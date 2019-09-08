<?php

//get command line parameters
if( isset($argv)){
	$define = array();
	$define['DEV'] = false;
	for($i=1 ; $i < count($argv) ; $i++)
	{
		switch(strtolower($argv[$i]))
		{
			case '--dev':
			case '-d':
				define('DEV', true);

				break;
			
			
		}

	}
	
}
//set Defaults constant for command line
@define('DEV', false);




 define('DS', DIRECTORY_SEPARATOR);
 //media dir
 define('PODCAT_PATH', __DIR__ . DS . 'podcast' . DS );
 //telegram channel
 define('CHAT_ID', "@DrHolakoueee");
 //bot api key
 define('BOT_TOKEN', '***');
 //current directory
 define('CD', __DIR__ . DS);
 


 
 //create logger 
require CD . 'vendor' . DS. 'autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('system');
$log->pushHandler(new StreamHandler(CD .'your.log', Logger::WARNING));


require "simplebot.php";
require "MP3File.php";
require "holaqueePodcast.php";


$hp = new holaqueePodcast();
$hp->downloadFiles();
$hp->uploadToTelegram();