
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "AIzaSyBEycUxZYCZ3FpzMyD6y-dx46PFRoX6fL",
    // Add other bootstrap parameters as needed, using camel case.
    // Use the 'v' parameter to indicate the version to load (alpha, beta, weekly, etc.)
  });


  let map;

async function initMap() {
  //@ts-ignore
  const { Map } = await google.maps.importLibrary("maps");

  map = new Map(document.getElementById("map"), {
    center: { lat: -34.397, lng: 150.644 },
    zoom: 8,
  });
}

initMap();


function obtenerUbicacion() {
    if (navigator.geolocation) {
        // Obtener la ubicación actual del dispositivo
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitud = position.coords.latitude;
            var longitud = position.coords.longitude;

            // Mostrar la latitud y longitud en la vista
            document.getElementById('latitud').innerText = latitud;
            document.getElementById('longitud').innerText = longitud;
            // Asignar los valores de longitud y latitud a los inputs
            document.getElementById('latituds').value = latitud;
            document.getElementById('longituds').value = longitud;
            // Mostrar la ubicación en el mapa de Google
            var ubicacion = {
                lat: latitud,
                lng: longitud
            };
            map.setCenter(ubicacion);
            var marker = new google.maps.Marker({
                position: ubicacion,
                map: map,
                title: 'Ubicación Actual'
            });
        }, function() {
            alert('No se pudo obtener la ubicación.');
        });
    } else {
        alert('La geolocalización no está disponible en este navegador.');
    }
}