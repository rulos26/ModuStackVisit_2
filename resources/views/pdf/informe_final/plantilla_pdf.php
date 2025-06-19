<div style="border: 2px solid rgb(175, 0, 0); padding: 12px;">
    <table cellpadding="5" style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td width="100%" style="border: 1px solid rgb(175,0,0); text-align: center;">
                <?php if (!empty($logo_b64)): ?>
                    <img src="<?= $logo_b64 ?>" alt="Logo" style=" width: 100%; height:103px;">
                <?php else: ?>
                    <span style="color: #888;">Logo no disponible</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?= htmlspecialchars($cedula) ?>
</div>