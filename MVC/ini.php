<!-- Se determina y escribe la localizacion -->
<div id='ubicacion'></div>
<script type="text/javascript">
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(mostrarUbicacion);
	} else {alert("�Error! Este navegador no soporta la Geolocalizaci�n.");}
function mostrarUbicacion(position) {
    var times = position.timestamp;
	var latitud = position.coords.latitude;
	var longitud = position.coords.longitude;
    var altitud = position.coords.altitude;	
	var exactitud = position.coords.accuracy;	
	var div = document.getElementById("ubicacion");
	div.innerHTML = "Timestamp: " + times + "<br>Latitud: " + latitud + "<br>Longitud: " + longitud + "<br>Altura en metros: " + altitud + "<br>Exactitud: " + exactitud;}	
function refrescarUbicacion() {	
	navigator.geolocation.watchPosition(mostrarUbicacion);}	
</script>

<!-- Se escribe un mapa con la localizacion anterior-->
<div id="demo"></div>
<div id="mapholder"></div>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">
var x=document.getElementById("demo");
cargarmap();
function cargarmap(){
navigator.geolocation.getCurrentPosition(showPosition,showError);

function showPosition(position)
  {
	  alert("lat:"+position.coords.latitude+"\nLon:"+position.coords.longitude)
  lat=position.coords.latitude;
  lon=position.coords.longitude;
  latlon=new google.maps.LatLng(lat, lon)
  mapholder=document.getElementById('mapholder')
  mapholder.style.height='250px';
  mapholder.style.width='500px';
  var myOptions={
  center:latlon,zoom:17,
  mapTypeId:google.maps.MapTypeId.ROADMAP,
  mapTypeControl:false,
  navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
  };
  var map=new google.maps.Map(document.getElementById("mapholder"),myOptions);
  var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
  }
function showError(error)
  {
  switch(error.code) 
    {
    case error.PERMISSION_DENIED:
      x.innerHTML="Denegada la peticion de Geolocalizaci�n en el navegador."
      break;
    case error.POSITION_UNAVAILABLE:
      x.innerHTML="La informaci�n de la localizaci�n no esta disponible."
      break;
    case error.TIMEOUT:
      x.innerHTML="El tiempo de petici�n ha expirado."
      break;
    case error.UNKNOWN_ERROR:
      x.innerHTML="Ha ocurrido un error desconocido."
      break;
    }
  }}
</script>