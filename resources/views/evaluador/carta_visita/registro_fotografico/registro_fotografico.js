// Acceder al dispositivo de la cámara
navigator.mediaDevices
  .getUserMedia({ video: true })
  .then(function (stream) {
    var video = document.getElementById("video");
    video.srcObject = stream;
    video.play();
  })
  .catch(function (err) {
    console.log("Ocurrió un error al acceder a la cámara: " + err);
  });

// Capturar la fotografía
function tomarFoto() {
  var video = document.getElementById("video");
  var canvas = document.createElement("canvas");
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  var context = canvas.getContext("2d");
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  // Convertir la fotografía a formato base64
  var dataURL = canvas.toDataURL("image/jpeg");

  // Enviar la fotografía al servidor para guardarla como imagen
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "guardar.php");
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onload = function () {
    if (xhr.status === 200) {
        //window.location.href = "firma.php";
        //alert("Fotografía guardada exitosamente.");
    } else {
      alert("Error al guardar la fotografía.");
    }
  };

  xhr.send("image=" + encodeURIComponent(dataURL));

  navigator.mediaDevices
    .getUserMedia({ video: true })
    .then(function (stream) {
      var video = document.getElementById("video");
      video.srcObject = stream;
      video.stop();
    })
    .catch(function (err) {
      console.log("Ocurrió un error al acceder a la cámara: " + err);
    });
}

// Suponiendo que tienes un elemento de video en tu HTML con el id "videoElement"
var videoElement = document.getElementById("video");

// Detener la transmisión de la cámara
function detenerCamara() {
  if (videoElement.srcObject) {
    var stream = videoElement.srcObject;
    var tracks = stream.getTracks();

    tracks.forEach(function (track) {
      track.stop();
    });

    videoElement.srcObject = null;
  }
}
