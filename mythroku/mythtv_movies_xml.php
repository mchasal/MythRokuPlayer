<?php

//get the local info from the settings file
require_once './settings.php';

//make a connection to the mysql sever
$db_handle = mysql_connect($MysqlServer, $MythTVdbuser, $MythTVdbpass);
$db_found = mysql_select_db($MythTVdb, $db_handle);

//set the stream id to some abitary number 
$counter = 1000;

//define query for sorting the records, only get files that are .mp4/.m4v
if ($db_found) {

	if (isset($_GET['sort']) && $_GET['sort'] == 'year') {
		$SQL = "SELECT * FROM videometadata WHERE filename LIKE '%.mp4' OR filename LIKE '%.m4v' ORDER BY year DESC ";
	} elseif (isset($_GET['sort']) && $_GET['sort'] == 'title'){
		$SQL = "SELECT * FROM videometadata WHERE filename LIKE '%.mp4' OR filename LIKE '%.m4v' ORDER BY title ASC ";
	} elseif (isset($_GET['sort']) && $_GET['sort'] == 'genre'){
		$SQL = "SELECT * FROM videometadata WHERE filename LIKE '%.mp4' OR filename LIKE '%.m4v' ORDER BY category ASC";
	} else {
		$SQL = "SELECT * FROM videometadata WHERE filename LIKE '%.mp4' OR filename LIKE '%.m4v'";
	}

//grab the data
$result = mysql_query($SQL);
$num_rows = mysql_num_rows($result);
	
//print the xml header
print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?> 
	<feed>
	<!-- resultLength indicates the total number of results for this feed -->
	<resultLength>" . $num_rows . "</resultLength>
	<!-- endIndix  indicates the number of results for this *paged* section of the feed -->
	<endIndex>" . $num_rows . "</endIndex>";

//print out all the records in xml format for roku to read 
while ($db_field = mysql_fetch_assoc($result) ) {

	$genrenum = mysql_fetch_assoc(mysql_query("SELECT idgenre FROM videometadatagenre where idvideo='" . $db_field['intid'] . "' "));
	if ($genrenum['idgenre'] == 0 ) { $genrenum['idgenre'] = 22; }
        $genre = mysql_fetch_assoc(mysql_query("SELECT genre FROM videogenre where intid='" . $genrenum['idgenre'] . "' "));

		print "	
		<item sdImg=\"http://www.nevill.uk.net/mythweb/mythroku/image.php?image=" . rawurlencode($db_field['coverfile']) . "\" hdImg=\"http://www.nevill.uk.net/mythweb/mythroku/image.php?image=" . rawurlencode($db_field['coverfile']) . "\">
			<title>" . htmlspecialchars(preg_replace('/[^(\x20-\x7F)]*/','', $db_field['title'] )) . "</title>
			<contentId>" . $counter++ . "</contentId>
			<contentType>Movies</contentType>
			<contentQuality>". $RokuDisplayType . "</contentQuality>
			<media>
				<streamFormat>mp4</streamFormat>
				<streamQuality>". $RokuDisplayType . "</streamQuality>
				<streamBitrate>". $BitRate . "</streamBitrate>
				<streamUrl>" . $WebServer . "/data/video/" . rawurlencode($db_field['filename']) ."</streamUrl>
			</media>
			<synopsis>" . htmlspecialchars(preg_replace('/[^(\x20-\x7F)]*/','', $db_field['plot'] )) . "</synopsis>	
			<genres>" . $genre['genre'] . "</genres>
			<runtime>" .$db_field['length'] . "</runtime>
			<date>Year: " . $db_field['year'] . "</date>
			<tvormov>movie</tvormov>
			<starrating>" . $db_field['userrating'] * 10 ."</starrating>
		</item>";	
		}	
	//}
	
print "</feed>";


	}

//throw error if can not connect to database.
else {
print "Database NOT Found ";

}

//close mysql pointer
mysql_close($db_handle);

?>

