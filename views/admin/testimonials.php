<?php
$page_title = 'Gestión de Testimonios';
$unread_count = $unread_messages ?? 0;
$filter = $filter ?? 'all';
$counts = $counts ?? ['all'=>0,'pending'=>0,'approved'=>0,'rejected'=>0];
ob_start();
?>
<div class="testimonials-summary" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <?php foreach (['all'=>'Total','pending'=>'Pendientes','approved'=>'Aprobados','rejected'=>'Rechazados'] as $k => $label):
        $cls = $k === 'pending' ? 'pending' : ($k === 'approved' ? 'approved' : ($k === 'rejected' ? 'rejected' : ''));
        $active = ($filter === $k) ? 'border-color:var(--primary);background:rgba(37,99,235,0.05);' : '';
        $numColor = $k === 'pending' ? 'var(--warning)' : ($k === 'approved' ? 'var(--success)' : ($k === 'rejected' ? 'var(--danger)' : 'var(--primary)'));
    ?>
    <a href="/admin.php?route=testimonials&filter=<?= $k ?>" style="background:var(--light);border:2px solid transparent;border-radius:var(--radius-md);padding:20px;text-align:center;text-decoration:none;color:var(--text-dark);transition:all .2s;<?= $active ?>">
        <span style="display:block;font-size:28px;font-weight:800;font-family:'Outfit';color:<?= $numColor ?>;"><?= $counts[$k] ?? 0 ?></span>
        <span style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;"><?= $label ?></span>
    </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div class="card-header"><h2><?php
        $labels = ['all'=>'Todos los Testimonios','pending'=>'Pendientes de Revisión','approved'=>'Aprobados','rejected'=>'Rechazados'];
        echo $labels[$filter] ?? 'Testimonios';
    ?></h2></div>
    <?php if (empty($items)): ?>
        <div style="text-align:center;padding:40px;color:var(--text-muted);">
            <i data-lucide="message-circle-off" style="width:40px;height:40px;stroke-width:1.5;margin-bottom:12px;color:var(--primary);"></i>
            <p>No hay testimonios <?= $filter !== 'all' ? 'en esta categoría' : 'registrados' ?>.</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:16px;">
            <?php foreach ($items as $t): ?>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;padding:20px;border:1px solid var(--border);border-radius:var(--radius-md);background:var(--light);<?php
                    echo $t['status']==='pending'?'border-left:4px solid var(--warning);':($t['status']==='approved'?'border-left:4px solid var(--success);':'border-left:4px solid var(--danger);');
                ?>">
                    <div style="display:flex;gap:16px;flex:1;min-width:0;">
                        <?php if (!empty($t['photo_url'])): ?>
                            <img src="../<?= htmlspecialchars($t['photo_url']) ?>" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:rgba(13,148,136,0.12);color:#0d9488;"><i data-lucide="circle-user-round"></i></div>
                        <?php endif; ?>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                                <strong><?= htmlspecialchars($t['name']) ?></strong>
                                <?php $sl=['pending'=>'Pendiente','approved'=>'Aprobado','rejected'=>'Rechazado']; $sc=['pending'=>'background:#fef3c7;color:#92400e;','approved'=>'background:#d1fae5;color:#065f46;','rejected'=>'background:#fee2e2;color:#991b1b;']; ?>
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;<?= $sc[$t['status']] ?>"><?= $sl[$t['status']] ?></span>
                            </div>
                            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--text-muted);margin-bottom:8px;">
                                <span><i data-lucide="mail" style="width:13px;height:13px;"></i> <?= htmlspecialchars($t['email']) ?></span>
                                <?php if (!empty($t['phone'])): ?><span><i data-lucide="phone" style="width:13px;height:13px;"></i> <?= htmlspecialchars($t['phone']) ?></span><?php endif; ?>
                                <span><i data-lucide="calendar" style="width:13px;height:13px;"></i> <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></span>
                            </div>
                            <p style="font-size:13.5px;color:var(--text-muted);line-height:1.6;margin:0;"><?= htmlspecialchars($t['message']) ?></p>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
                        <?php if ($t['status'] !== 'approved'): ?>
                            <a href="/admin.php?route=testimonials&action=approve&id=<?= $t['id'] ?>&filter=<?= $filter ?>" class="btn-admin-action approve" onclick="return confirm('¿Aprobar este testimonio?');" style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;text-decoration:none;background:#d1fae5;color:#065f46;"><i data-lucide="check-circle" style="width:16px;height:16px;"></i> Aprobar</a>
                        <?php endif; ?>
                        <?php if ($t['status'] !== 'rejected'): ?>
                            <a href="/admin.php?route=testimonials&action=reject&id=<?= $t['id'] ?>&filter=<?= $filter ?>" class="btn-admin-action reject" onclick="return confirm('¿Rechazar?');" style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;text-decoration:none;background:#fef3c7;color:#92400e;"><i data-lucide="x-circle" style="width:16px;height:16px;"></i> Rechazar</a>
                        <?php endif; ?>
                        <a href="/admin.php?route=testimonials&action=delete&id=<?= $t['id'] ?>&filter=<?= $filter ?>" class="btn-admin-action delete" onclick="return confirm('¿Eliminar? Esta acción no se puede deshacer.');" style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;text-decoration:none;background:#fee2e2;color:#991b1b;"><i data-lucide="trash-2" style="width:16px;height:16px;"></i> Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
