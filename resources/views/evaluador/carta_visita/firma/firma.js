 // Variables para el canvas y el contexto
 var canvas = document.getElementById('canvas');
 var ctx = canvas.getContext('2d');
 var isDrawing = false;
 var lastX = 0;
 var lastY = 0;

 // Eventos para dibujar en el canvas
 canvas.addEventListener('mousedown', function (e) {
     isDrawing = true;
     [lastX, lastY] = [e.offsetX, e.offsetY];
 });

 canvas.addEventListener('mousemove', function (e) {
     if (isDrawing) {
         drawLine(lastX, lastY, e.offsetX, e.offsetY);
         [lastX, lastY] = [e.offsetX, e.offsetY];
     }
 });

 canvas.addEventListener('mouseup', function () {
     isDrawing = false;
 });

 canvas.addEventListener('mouseout', function () {
     isDrawing = false;
 });

 // Función para dibujar una línea en el canvas
 function drawLine(x1, y1, x2, y2) {
     ctx.beginPath();
     ctx.strokeStyle = 'black';
     ctx.lineWidth = 2;
     ctx.moveTo(x1, y1);
     ctx.lineTo(x2, y2);
     ctx.stroke();
     ctx.closePath();
 }

 // Función para guardar la firma
 function guardarFirma() {
     // Obtener la imagen en formato base64
     var dataURL = canvas.toDataURL('image/png');

     // Enviar la firma al servidor para guardarla como imagen
     var xhr = new XMLHttpRequest();
     xhr.open('POST', 'guardar.php');
     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
     xhr.onload = function() {
         if (xhr.status === 200) {
             // Obtener la ruta de la imagen guardada
             var rutaImagen = xhr.responseText;
             document.getElementById('firmaImg').src = xhr.responseText;

             // Mostrar la imagen generada en el modal
             var imagenGenerada = new Image();
             
             imagenGenerada.style.width = '100%'; // Ajustar el ancho de la imagen al modal
             document.getElementById('modalFirma').getElementsByClassName('modal-body')[0].innerHTML = ''; // Limpiar el contenido del modal
             document.getElementById('modalFirma').getElementsByClassName('modal-body')[0].appendChild(imagenGenerada);
            
             // Cerrar el modal al guardar la firma
             var modal = bootstrap.Modal.getInstance(document.getElementById('modalFirma'));
             modal.hide();
             window.location.href = '../ubicacion/ubicacion.php'; // Cambia 'nueva_pagina.html' por la URL que desees
         }
     };
     xhr.send('image=' + encodeURIComponent(dataURL));
     
                  
 }