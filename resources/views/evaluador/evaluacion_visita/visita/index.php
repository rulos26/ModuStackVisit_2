<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
?>
<style>
.container-visita {
    max-width: 600px;
    margin: 40px auto;
    font-family: Arial, sans-serif;
}
.card-visita {
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 30px 30px 10px 30px;
    background: #fff;
}
.card-visita h5 {
    font-size: 1.5em;
    margin-bottom: 18px;
    color: #4361ee;
}
.card-visita .logo {
    display: block;
    margin: 0 auto 18px auto;
    max-width: 180px;
    max-height: 120px;
}
.card-visita .alert {
    background: #e7f3fe;
    border: 1px solid #b3e5fc;
    color: #31708f;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 18px;
}
.card-visita .form-label { font-weight: bold; }
.card-visita .btn { font-size: 1.1em; border-radius: 6px; }
.card-visita .card-footer { text-align: right; color: #888; font-size: 0.95em; margin-top: 18px; }
</style>
<div class="container-visita">
    <div class="card-visita">
        <img src="../../../../../public/images/logo.jpg" alt="Logotipo de la empresa" class="logo">
        <h5 class="card-title">VISITA DOMICILIARÍA</h5>
        <div class="alert alert-info">
            <strong>PROTECCIÓN DE DATOS PERSONALES:</strong>
            Al suministrar sus datos personales en este formulario AUTORIZA su tratamiento de forma expresa y voluntaria a la empresa Grupo de Tareas Empresariales. Le informamos que estos serán tratados conforme a lo previsto en la ley 1581 del 2012 y serán incluidos en una base de datos cuyo responsable es Grupo de Tareas Empresariales. La finalidad de la recolección es la gestión de siniestros y el trámite de reclamos ante las compañías de seguros. Usted podrá revocar su autorización en cualquier momento, consultar su información personal y ejercer sus derechos de conocer, actualizar, rectificar, corregir, suprimir o revocar su autorización enviando un email a: grpte@hotmail.com
        </div>
        <form action="session.php" method="POST" id="formDocumento" autocomplete="off">
            <div class="mb-3">
                <label for="id_cedula" class="form-label">Número de Documento:</label>
                <input type="number" class="form-control" id="id_cedula" name="id_cedula" required min="1" autocomplete="off">
                <div class="invalid-feedback">
                    Por favor ingrese un número de documento válido.
                </div>
            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="Empezar" id="btnEnviar" disabled>
            </div>
        </form>
        <div class="card-footer text-body-secondary">
            © 2024 V0.01
        </div>
    </div>
</div>
<script>
// Automatización: Habilitar el botón solo si el campo tiene valor válido
const inputCedula = document.getElementById('id_cedula');
const btnEnviar = document.getElementById('btnEnviar');
inputCedula.addEventListener('input', function() {
    if (inputCedula.value.length > 0 && parseInt(inputCedula.value) > 0) {
        btnEnviar.disabled = false;
        inputCedula.classList.remove('is-invalid');
    } else {
        btnEnviar.disabled = true;
        inputCedula.classList.add('is-invalid');
    }
});
</script>
<?php
$contenido = ob_get_clean();
include dirname(__DIR__, 3) . '/layout/dashboard.php';
?>