<?php


class holaqueePodcast
{
  private $url = "http://www.razhavaniazha.com/page/";
	public $downloadedList = array();

	private function findUrls ($max)
	{
		// look in $url and find all urls that are mediafire and a mp3 file.
		$urls = array();
		global $log;
		
		for($i = 1 ; $i <= $max; $i++)
		{
			$content = file_get_contents($this->url . $i);
			$matches = array();
			preg_match_all("/(http:\/\/www\.)?(mediafire\.com.*?mp3)/i",$content,$matches);
			
			//remove duplicates from url if any with array_unique
			foreach (array_unique($matches[0]) as $v)
			{
				if( ! $this->mediaExists( basename($v) ) )
				{
					$urls[] = $v;
				}
				else
				{
					echo "**** $i ****";
					break 2;
				}
			}
		}
		return array_reverse($urls);
	}
	
	private function mediaExists($filename)
	{
		return file_exists( PODCAT_PATH . $filename );
	}
	
	/**
	 * 		download files to podcast_path dirctory.
	 */
	public function downloadFiles()
	{
		//get all urls of n page
		$urlPodcast = $this->findUrls(3);

		//download all files of not exists in podcast and add to download list
		for($i = 0; $i < count($urlPodcast); $i++)
		{
			
			//check if file does not exist then download the file 
			if(DEV)
			{
				echo "Log: download " . PODCAT_PATH . basename($urlPodcast[$i]) . "\n";
				$this->downloadedList[] = basename($urlPodcast[$i]);
			}else
			{
				
				shell_exec('wget -P ' . PODCAT_PATH . ' ' . $urlPodcast[$i]);
				if( ! $this->mediaExists( basename($urlPodcast[$i]) ) )
					$log->warning("download failed: " ,array('url'=>basename($urlPodcast[$i])));
				else
					$this->downloadedList[] = basename($urlPodcast[$i]);
			}	
		}
	}
	
	/*
	 upload files to telegram
	*/
	function uploadToTelegram()
	{
		foreach( $this->downloadedList as $mp3filename)
		{
			if( stripos($mp3filename , "razha") )
			{
				$title = "Razha Va Niazha";

			}
			else if( stripos($mp3filename , "meybodi") )
			{
				$title = "Meybodi";
			}
			else
			{
				$title = "Holakouee";
			}
		
			
			$performer="";
			if( stripos($mp3filename , "eve") )
			{
				$performer = " Evening ";
			}
			else if ( stripos($mp3filename , "mor") )
			{
				$performer = " Morning ";
			}
		
			$performer .= $this->getTime( $mp3filename ) ;

			if(DEV)
			{
				echo "Log: send to telegram " . print_r(array(
				"chat_id"=>CHAT_ID,
				"#audio"=> PODCAT_PATH . $mp3filename ,
				"duration"=> 0,
				"title" => $title,
				"performer" => $performer,
				"disable_notification" => "false"
				),true) . "<br>\n\n";
			
			}
			else
			{
				
				if( !file_exists( PODCAT_PATH . $mp3filename) )
				{
					error_log("file Does not exist" . PODCAT_PATH . $mp3filename);
					echo "file Does not exist" .PODCAT_PATH . $mp3filename. "\n";
					continue;
				}
				
				$mp3file = new MP3File(PODCAT_PATH . $mp3filename);
				$duration = $mp3file->getDurationEstimate();
				
				echo request_sendfile("sendAudio",array(
				"chat_id"=>CHAT_ID,
				"#audio"=> PODCAT_PATH . $mp3filename ,
				"duration"=> $duration,
				"performer" => $performer,
				"title" => $title,
				
				));
				
				
				echo request_sendfile("sendphoto",array(
				"chat_id"=>CHAT_ID,
				"#photo"=> CD ."Holakouee_Farhang.JPG" ,
				"caption"=> "فرهنگ هُلاکوئیِ، جامعه‌شناس، اقتصاددان، مشاور، روان‌شناس، نویسنده و روان‌درمانگر ایرانی ـ آمریکایی است.",
				));
			}	
		}
	}

	function getTime($filename)
	{
		$timeex = explode('.',$filename);
		$d = $timeex[0];
		$m = $timeex[1];
		$y = substr($timeex[2],0,4);
		return @date("d M y",@mktime(0,0,0,$m,$d,$y));
	}
}