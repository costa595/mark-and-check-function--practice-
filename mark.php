<?php

// require_once("../connect.php");

?>

<!DOCTYPE HTML>
<head>
<title>Mark on the Map</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type ="text/javascript" src="../js/markerclusterer.js"></script>

<!-- <script type ="text/javascript" src="../js/data.json"></script> -->

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>

<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="markandcheck.css">

</head>

<?php

error_reporting(0);
$user="root";
$pass="";

$connection=mysql_connect("localhost",$user,$pass);

mysql_select_db('dvt',$connection);
//----------------------------ROAD------------------

if (isset($_COOKIE['your_id_r'])) 
{	
	$user_id_r = NULL;
	$user_id_r = $_COOKIE['your_id_r']; 

	$coords_r=mysql_fetch_array(mysql_query("SELECT lat,lng FROM Road_users WHERE id=".$user_id_r." LIMIT 1",$connection));
	
	$user_lat = $coords_r['lat'];
	$user_lng = $coords_r['lng'];
} 

//-------------------------AIR---------------------

if (isset($_COOKIE['your_id_a'])) 
{	
	$user_id_a = NULL;			
	$user_id_a = $_COOKIE['your_id_a'];

	$coords_a=mysql_fetch_array(mysql_query("SELECT lat,lng FROM Air_users WHERE id=".$user_id_a." LIMIT 1",$connection));
	
	$user_lat = $coords_a['lat'];
	$user_lng = $coords_a['lng'];
} 

//---------------------------------------LOAD MAP--------------------

?>

<script language="JavaScript">

var marker = new Array();
 	markers = [];

var MY_MAPTYPE_ID = "mystyle";

function initialize() {

    var center = new google.maps.LatLng("<?=$user_lat?>", "<?=$user_lng?>");
	
	var stylez = [
		{
		featureType: "all",
		stylers: [{ hue: "#87CEFA" }]
		}, {
	    featureType: "water",
	    elementType: "geometry",
	    stylers: [
	      { lightness: 41 }
	    ]
	  }
	
	];//-----stylez
	
    var mapOptions = {

        zoom: 3,

        center: center,

        mapTypeControlOptions: {
      		 mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
    	},
        mapTypeId: MY_MAPTYPE_ID

    };//-----mapOptions
	

  	map = new google.maps.Map(document.getElementById("map"), mapOptions);

 	var styledMapOptions = {name: "style"};
 
  	var jayzMapType = new google.maps.StyledMapType(stylez, styledMapOptions);
 
    map.mapTypes.set(MY_MAPTYPE_ID, jayzMapType);
  
 <?
 		
//-----------------------------------------ADD MARKER--------------------------	
	
if((isset($_COOKIE['your_id_a'])) || (isset($_COOKIE['your_id_r'])))
	{
	 	echo 'google.maps.event.addListener(map, "click", function (event){addMarker(event.latLng);});';
	} 
		
//-------------------------------------SHOWING AIR MARKERS------------------------------		
$air_markers=mysql_query("SELECT * FROM Air_users ORDER BY id ",$connection);        		
while ($air_marker = mysql_fetch_array($air_markers))
{
	$id_a = $air_marker['id'];
	
	$name_a = $air_marker['name'];
	$address_a = $air_marker['address'];
	
	$day_a = $air_marker['day'];
	$month_a = $air_marker['month'];
	$hour_a = $air_marker['hour'];
	$min_a = $air_marker['min'];
	
	$VK_link_a = $air_marker['vk_link'];
	$VK_photo_a = $air_marker['vk_photo'];
	
	$users_lats_a = $air_marker['lat'];
	$users_lngs_a = $air_marker['lng'];
	
	//-------------------------creation of the markers
	
	if (($users_lats_a!=NULL) && ($users_lngs_a!=NULL)) 
	{
	echo 'marker["'.$id_a.'"] = new google.maps.Marker({map:map, title:"'.$name_a." ".$address_a.'", position: new google.maps.LatLng("'.$users_lats_a.'","'.$users_lngs_a.'")});';
	} 
	
	//------------------------creation of the infos

?>
					
	google.maps.event.addListener(marker['".$id_a."'], 'click', function() 
		{
			//-------------------------BOUNCE------------------------------
			if (marker['".$id_a."'].getAnimation() != null) 
			  	{
					marker['".$id_a."'].setAnimation(null);
						
				} else {
						  	
					marker['".$id_a."'].setAnimation(google.maps.Animation.BOUNCE);
				}
					  
			window.setTimeout(function() {map.panTo(marker['".$id_a."'].getPosition());}, 25);
					  	
				//----------------------------------------------------------------------------
		  	
			check();
		  
		 	$('#blog').html('<table id=\"first\"><tr> <td> <div class=\"pos1\"> <h1>".$name_a."</h1></div> </td> <td> <img src=".$VK_photo_a." id=\"ava\"  style=\"width:72px; height:72px; border-radius:5px;\"> </td> </tr></table>  <table id=\'second\'> <tr><td id=\'one\'><div class=\"statText\"> <h3>".$name_a."\'s position:</h3></div> <div class=\"text\"><h3>".$address_a."</h3></div> </td> </tr> <tr> <td id=\'two\'><div class=\"statText1\"><h3>Date:</h3> </div> <div class=\"text1\"><h3> ".$day_a."</h3></div><div class=\"statText2\"><h3> Month: </h3></div> <div class=\"text2\"> <h3>".$month_a."</h3></div> <div class=\"statText3\"><h3> Time: </h3> </div><div class=\"text3\"> <h3>".$hour_a." : ".$min_a."</h3></div> </td> </tr> <tr><td id=\'three\'><div class=\"text4\"><h3>".$name_a."\'s contacts:</h3></div></td></tr></table> <a href = \"http://".$VK_link_a."\"><img src=\"../images/vk.jpg\" id=\"vk\"></a> <a href = \"http://".$FC_link_a."\"><img src=\"../images/f.png\" id=\"fc\"></a> <a href = \"http://".$TW_link_a."\"><img src=\"../images/t.jpg\" id=\"tw\"></a> ');

		});

	markers.push(marker['".$id_a."']); 
<?			
	// -------------  allowing deletion for one person only of his own marker------
	
	// if ($user_id_a===$row['id_a'])
	// {

 // echo "  google.maps.event.addListener(marker['".$id_a."'], 'rightclick', function (){

	// 	marker['".$id_a."'].setMap(null);
				
	//  }); ";

	// } 
	
}
	  
// -----------------------------------------------------------------------	
//------------------------------------**********************************************
//-------------------------------------SHOWING ROAD MARKERS------------------------------	
$road_markers=mysql_query("SELECT * FROM Road_users ORDER BY id ",$connection);

while ($road_marker = mysql_fetch_array($road_markers))
{
	$id_r = $road_marker['id'];
	
	$name_r = $road_marker['name'];
	$address_r = $road_marker['address'];
	
	$day_r = $road_marker['day'];
	$month_r = $road_marker['month'];
	$hour_r = $road_marker['hour'];
	$min_r = $road_marker['min'];
	
	$VK_link_r = $road_marker['vk_link'];
	$VK_photo_r = $road_marker['vk_photo'];
	
	$users_lats_r = $road_marker['lat'];
	$users_lngs_r = $road_marker['lng'];
	
	//------------------------------creation of the markers
	
	if (($users_lats_r!=NULL) && ($users_lngs_r!=NULL)) 
	{
	echo 'marker["'.$id_r.'"] = new google.maps.Marker({map:map, title:"'.$name_r." ".$address_r.'", position: new google.maps.LatLng("'.$users_lats_r.'","'.$users_lngs_r.'")});';
	} 
	
	//------------------------------creation of the infos
?>
					
	google.maps.event.addListener(marker['".$id_r."'], 'click', function() 
		{
			//-------------------------------BOUNCE---------------------------------------------
	
			if (marker['".$id_r."'].getAnimation() != null) 
		  		{
					marker['".$id_r."'].setAnimation(null);
					    
				} else {
					
					marker['".$id_r."'].setAnimation(google.maps.Animation.BOUNCE);
				}
	  
	  		window.setTimeout(function() {map.panTo(marker['".$id_r."'].getPosition());}, 25);
			//----------------------------------------------------------------------------
	
		  	check();	
  
 		    $('#blog').html('<table id=\"first\"><tr> <td> <div class=\"pos1\"> <h1>".$name_r."</h1></div> </td> <td> <img src=".$VK_photo_r." id=\"ava\"  style=\"width:72px; height:72px; border-radius:5px;\"> </td> </tr></table>  <table id=\'second\'> <tr><td><div class=\"statText\"> <h3>".$name_r."\'s position:</h3></div> <div class=\"text\"><h3>".$address_r."</h3></div> </td> </tr> <tr> <td><div class=\"statText1\"><h3>Date:</h3> </div> <div class=\"text1\"><h3> ".$day_r."</h3></div><div class=\"statText2\"><h3> Month: </h3></div> <div class=\"text2\"> <h3>".$month_r."</h3></div> <div class=\"statText3\"><h3> Time: </h3> </div><div class=\"text3\"> <h3>".$hour_r." : ".$min_r."</h3></div> </td> </tr> <tr><td><div class=\"text4\"><h3>".$name_r."\'s contacts:</h3></div></td></tr></table> <a href = \"http://".$VK_link_r."\"><img src=\"../images/vk.jpg\" id=\"vk\"></a> <a href = \"http://".$FC_link_r."\"><img src=\"../images/f.png\" id=\"fc\"></a> <a href = \"http://".$TW_link_r."\"><img src=\"../images/t.jpg\" id=\"tw\"></a> ');
  
  		});

	markers.push(marker['".$id_r."']); 
	var  markerClusterer = new MarkerClusterer(map, markers,{maxZoom:20});
<?			
	// -------------  allowing deletion for one person only of his own marker------
	
	// if ($user_id_r===$row['id_r'])
	// {
 // 		echo "  google.maps.event.addListener(marker['".$id_r."'], 'rightclick', function (){marker['".$id_r."'].setMap(null);}); ";
	// } 
	  
// -----------------------------------------------------------------------	

	
// the beginning of the clusterisation

}
?>


function check()
{
	if(window.screen.width > 600)
		{
			$('#map').animate({width:70+'%'}, 1000);
						
		} else {
			$('#map').animate({height:200+'px'}, 1000);
		}
					
 	$('#blog').delay(800).fadeIn();
}

//-------------------------------------ADD ROAD MARKER FUNCTION---------------------
function addMarker(location) 
{
  	var  marker = new google.maps.Marker({position: location,map: map,});

var lat = location.lat();
var lng = location.lng(); 

$('#map').click(function() 
{
	$.ajax({
		type: 'POST',
		url: 'markandcheck.php',
		data: ({'latt':lat,
				'lngg':lng})
			});
			
	// window.location.reload();
});

<?php

 $lat = $_POST['latt'];
 $lng = $_POST['lngg'];

if (isset($_COOKIE['count_r']))
{

mysql_query("UPDATE Road_z_map SET lat=$lat, lng=$lng WHERE id='$user_id_r'",$connection);

} 

if (isset($_COOKIE['count_a'])) 
{

mysql_query("UPDATE Air_z_map SET lat=$lat, lng=$lng WHERE id='$user_id_a'",$connection);

} 

?>
}


//----------------------------------------------------------------------------
<?

if(($lat1==NULL)&&($lng1==NULL))
{

?>
	$('body').addClass('play1');
	
	setTimeout(function()
		{
			$('body').removeClass('play1');
		},4000);
	
<?}?>


</script>


<body onload="initialize()">

<div id="blog"></div>
<div id="map">  </div>
<div id="hello">Mark your destination!</div>

</body>

</html>