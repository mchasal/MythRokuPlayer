<?php

require_once './settings.php';

print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
<categories>

	<!-- banner_ad: optional element which displays an add at the top level category screen -->
	<banner_ad sd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/mythtv_logo_SD.png\" hd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/mythtv_logo_SD.png\"/>

	<category title=\"TV\" description=\"MythTV TV\" sd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/Mythtv_tv.png\" hd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/Mythtv_tv.png\">\n";

	//make a connection to the mysql sever
	$db_handle = mysql_connect($MysqlServer, $MythTVdbuser, $MythTVdbpass);
	$db_found = mysql_select_db($MythTVdb, $db_handle);
	if ($db_found) {

		if ($TVStyle == "title") {
			//define quiery for sorting the records
			$SQL = "SELECT DISTINCT title FROM recorded ORDER BY title ASC";
			//grab the data
			$result = mysql_query($SQL);
			$num_rows = mysql_num_rows($result);
			//reset pointer
			mysql_data_seek ( $result , 0 );
			while ($db_field = mysql_fetch_assoc($result)) {
				$cleantitle = htmlspecialchars(preg_replace('/[^(\x20-\x7F)]*/','', $db_field['title'] ));
				$qtitle = urlencode($db_field['title']);
				print "          <categoryLeaf title=\"" .  $cleantitle . "\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_group_xml.php?title=". $qtitle ."\"/>\n";
			}
		} elseif ($TVStyle == "recgroup") {
	                $SQL = "SELECT DISTINCT recgroup FROM recorded ORDER BY recgroup ASC";
       	         	//grab the data
                	$result = mysql_query($SQL);
                	$num_rows = mysql_num_rows($result);
                	//reset pointer
                	mysql_data_seek ( $result , 0 );

                	while ($db_field = mysql_fetch_assoc($result)) {
                        	print "           <categoryLeaf title=\"" .  $db_field['recgroup'] . "\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_group_xml.php?group=". $db_field['recgroup'] ."\"/>\n";
                	}
		} else {
			//Default case, just give some generic sorting
			print "	<categoryLeaf title=\"Title\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_tv_xml.php?sort=title\"/> 
				<categoryLeaf title=\"Genre\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_tv_xml.php?sort=genre\"/> 
				<categoryLeaf title=\"Channel\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_tv_xml.php?sort=channel\"/>";
		}
	} else {
		//throw error if cannot connect to database
		print "Database NOT Found ";
	}
	//close mysql pointer
	mysql_close($db_handle);


	print "        </category>

	<category title=\"Movies\" description=\"MythTV Movies\" sd_img=\"" . $WebServer . "/mythroku/images/Mythtv_movie.png\" hd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/Mythtv_movie.png\">
		<categoryLeaf title=\"Title\" description=\"\" feed=\"" . $WebServer . "/mythroku/mythtv_movies_xml.php?sort=title\"/> 
		<categoryLeaf title=\"Genre\" description=\"\" feed=\"" . $WebServer . "/mythroku/mythtv_movies_xml.php?sort=genre\"/> 
		<categoryLeaf title=\"Year\" description=\"\" feed=\"" . $WebServer . "/mythroku/mythtv_movies_xml.php?sort=year\"/> 
	</category>

	<category title=\"Settings\" description=\"Roku MythTV Settings\" sd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/Mythtv_settings.png\" hd_img=\"" . $WebServer . "/" . $MythRokuDir . "/images/Mythtv_settings.png\">
		<categoryLeaf title=\"Settings\" description=\"\" feed=\"" . $WebServer . "/" . $MythRokuDir . "/mythtv_tv.xml\"/> 
	</category>

 </categories>";

?>
