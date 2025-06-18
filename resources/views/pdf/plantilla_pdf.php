<?php
// Variables esperadas: $row1, $img_ubicacion_b64, $img_firma_b64, $img_perfil_b64, $row2, $row3, $row4, $img_ubicacion_path, $img_firma_path, $img_perfil_path
?>
<style>
.container { max-width: 800px; margin: 30px auto; }
.card { box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 10px; border: 1px solid #ddd; }
.card-body { padding: 30px; }
.alert-info { background: #e7f3fe; border: 1px solid #b3e5fc; color: #31708f; border-radius: 8px; padding: 20px; }
.alert-heading { font-size: 1.5em; margin-bottom: 10px; }
.text-center { text-align: center; }
.btn { display: inline-block; padding: 10px 24px; font-size: 1.1em; border-radius: 6px; text-decoration: none; }
.btn-primary { background: #4361ee; color: #fff; border: none; }
.img-section { display: flex; justify-content: center; gap: 20px; margin: 30px 0; }
.img-section img { max-width: 150px; max-height: 150px; border: 2px solid #888; border-radius: 8px; }
</style>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <div class="alert alert-info">
                <h4 class="alert-heading">Carta de Autorización</h4>
                <p>Esta es la sección personalizada para la gestión de la carta de autorización. Aquí puedes mostrar formularios, tablas o cualquier contenido específico relacionado con este módulo.</p>
            </div>
            <div class="img-section">
                <?php if (!empty($img_ubicacion_b64)): ?>
                    <div><strong>Ubicación</strong><br><img src="<?= $img_ubicacion_b64 ?>"></div>
                <?php endif; ?>
                <?php if (!empty($img_firma_b64)): ?>
                    <div><strong>Firma</strong><br><img src="<?= $img_firma_b64 ?>"></div>
                <?php endif; ?>
                <?php if (!empty($img_perfil_b64)): ?>
                    <div><strong>Perfil</strong><br><img src="<?= $img_perfil_b64 ?>"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<hr><h2>Debug de rutas de imágenes</h2>
<ul>
    <li><strong>Ubicación:</strong><br>
        Nombre BD: <?= htmlspecialchars($row2['nombre'] ?? '(sin valor)') ?><br>
        Ruta final: <?= htmlspecialchars($img_ubicacion_path) ?><br>
        <?= $img_ubicacion_b64 ? '<img src="' . $img_ubicacion_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>' ?>
    </li>
    <li><strong>Firma:</strong><br>
        Nombre BD: <?= htmlspecialchars($row3['nombre'] ?? '(sin valor)') ?><br>
        Ruta final: <?= htmlspecialchars($img_firma_path) ?><br>
        <?= $img_firma_b64 ? '<img src="' . $img_firma_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>' ?>
    </li>
    <li><strong>Perfil:</strong><br>
        Nombre BD: <?= htmlspecialchars($row4['nombre'] ?? '(sin valor)') ?><br>
        Ruta final: <?= htmlspecialchars($img_perfil_path) ?><br>
        <?= $img_perfil_b64 ? '<img src="' . $img_perfil_b64 . '" style="max-width:300px;max-height:200px;border:1px solid #333;">' : '<span style="color:red">Imagen no encontrada</span>' ?>
    </li>
</ul> 