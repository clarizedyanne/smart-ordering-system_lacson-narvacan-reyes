<?php
session_start();
require_once 'classes/Auth.php';
require_once 'classes/MLModel.php';

$auth = new Auth();
$auth->requireLogin();

// Run the full ML pipeline
$ml      = new MLModel();
$results = $ml->analyze();
$summary = $ml->getSummary($results);

$pageTitle = 'Staff Performance';
include 'partials/header.php';
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;margin:0;">
            <i class="bi bi-cpu-fill me-2 text-warning"></i>Staff Insights
        </h2>
        <p class="text-muted mb-0" style="font-size:.83rem;">
            ML-powered analysis using weighted scoring &amp; Min-Max normalization
        </p>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <div class="stat-value" style="font-size:1.1rem;">
                    <?= htmlspecialchars($summary['top_performer'] ?? '—') ?>
                </div>
                <div class="stat-label">Top Performer</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value"><?= $summary['total_staff'] ?></div>
                <div class="stat-label">Staff Analyzed</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-cart-check-fill"></i></div>
            <div>
                <div class="stat-value"><?= $summary['total_orders'] ?></div>
                <div class="stat-label">Total Orders Handled</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="stat-value"><?= $summary['avg_completion'] ?>%</div>
                <div class="stat-label">Avg Completion Rate</div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-bar-chart-steps me-2 text-warning"></i>Staff Performance Rankings</span>
        <span class="text-muted" style="font-size:.78rem;"><?= count($results) ?> staff member(s) analyzed</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Staff Member</th>
                    <th>Role</th>
                    <th>Orders Handled</th>
                    <th>Revenue Generated</th>
                    <th>Completion Rate</th>
                    <th>Cancelled</th>
                    <th>Score</th>
                    <th>Classification</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr><td colspan="9">
                        <div class="empty-state">
                            <i class="bi bi-cpu"></i>
                            <p>No staff data to analyze yet. Add orders first.</p>
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($results as $r): ?>
                    <tr <?= $r['rank'] === 1 ? 'style="background:rgba(251,191,36,.07);"' : '' ?>>
                        <td>
                            <?php if ($r['rank'] === 1): ?>
                                <span style="font-size:1.2rem;">🥇</span>
                            <?php elseif ($r['rank'] === 2): ?>
                                <span style="font-size:1.2rem;">🥈</span>
                            <?php elseif ($r['rank'] === 3): ?>
                                <span style="font-size:1.2rem;">🥉</span>
                            <?php else: ?>
                                <strong>#<?= $r['rank'] ?></strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:700;"><?= htmlspecialchars($r['staff_name']) ?></div>
                            <?php if ($r['last_order_date']): ?>
                                <div class="text-muted" style="font-size:.73rem;">
                                    Last active: <?= date('M d, Y', strtotime($r['last_order_date'])) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?= $r['role'] === 'admin' ? 'status-completed' : 'status-processing' ?>">
                                <?= ucfirst($r['role']) ?>
                            </span>
                        </td>
                        <td><strong><?= $r['total_orders'] ?></strong></td>
                        <td>₱<?= number_format($r['total_revenue'], 2) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="flex:1;background:var(--surface-2);border-radius:99px;height:7px;overflow:hidden;min-width:60px;">
                                    <div style="width:<?= $r['completion_rate'] ?>%;height:100%;background:#22c55e;border-radius:99px;"></div>
                                </div>
                                <span style="font-size:.82rem;font-weight:600;"><?= $r['completion_rate'] ?>%</span>
                            </div>
                        </td>
                        <td>
                            <?php if ($r['cancelled_orders'] > 0): ?>
                                <span style="color:#ef4444;font-weight:600;"><?= $r['cancelled_orders'] ?></span>
                            <?php else: ?>
                                <span class="text-muted">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-family:monospace;font-weight:700;font-size:.9rem;color:#6366f1;">
                                <?= number_format($r['ml_score'], 4) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $r['label_color'] ?> d-flex align-items-center gap-1" style="width:fit-content;padding:6px 10px;border-radius:20px;font-size:.75rem;">
                                <i class="bi <?= $r['label_icon'] ?>"></i>
                                <?= $r['performance_label'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Visual Score Bar Chart -->
<?php if (!empty($results)): ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill me-2 text-warning"></i>Score Comparison
            </div>
            <div class="card-body p-4">
                <?php foreach ($results as $r): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.83rem;">
                        <span style="font-weight:600;"><?= htmlspecialchars($r['staff_name']) ?></span>
                        <span style="font-family:monospace;color:#6366f1;"><?= number_format($r['ml_score'] * 100, 1) ?>%</span>
                    </div>
                    <div style="background:var(--surface-2);border-radius:99px;height:12px;overflow:hidden;">
                        <div style="width:<?= round($r['ml_score'] * 100, 1) ?>%;height:100%;border-radius:99px;
                            background:<?= $r['label_color'] === 'success' ? 'linear-gradient(90deg,#22c55e,#16a34a)' : ($r['label_color'] === 'primary' ? 'linear-gradient(90deg,#6366f1,#4f46e5)' : 'linear-gradient(90deg,#f59e0b,#d97706)') ?>;
                            transition:width .6s ease;">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-warning"></i>Orders per Staff Member
            </div>
            <div class="card-body p-4">
                <?php
                $totalAllOrders = array_sum(array_column($results, 'total_orders')) ?: 1;
                foreach ($results as $r):
                    $pct = round($r['total_orders'] / $totalAllOrders * 100, 1);
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.83rem;">
                        <span style="font-weight:600;"><?= htmlspecialchars($r['staff_name']) ?></span>
                        <span class="text-muted"><?= $r['total_orders'] ?> orders (<?= $pct ?>%)</span>
                    </div>
                    <div style="background:var(--surface-2);border-radius:99px;height:12px;overflow:hidden;">
                        <div style="width:<?= $pct ?>%;height:100%;border-radius:99px;background:linear-gradient(90deg,#f97316,#ea580c);transition:width .6s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Insight Callouts -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-lightbulb-fill me-2 text-warning"></i>Overall Insights
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <?php
            $topPerformers  = array_filter($results, fn($r) => $r['performance_label'] === 'Top Performer');
            $lowActivity    = array_filter($results, fn($r) => $r['performance_label'] === 'Low Activity');
            $bestRevenue    = $results[0] ?? null; 
            ?>

            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);">
                    <div style="font-weight:700;font-size:.85rem;color:#16a34a;margin-bottom:8px;">
                        <i class="bi bi-trophy-fill me-1"></i>Top Performer(s)
                    </div>
                    <?php foreach ($topPerformers as $tp): ?>
                        <div style="font-size:.85rem;">⭐ <?= htmlspecialchars($tp['staff_name']) ?> — Score: <?= number_format($tp['ml_score'], 4) ?></div>
                    <?php endforeach; ?>
                    <?php if (empty($topPerformers)): ?>
                        <div class="text-muted" style="font-size:.82rem;">No top performers yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.25);">
                    <div style="font-weight:700;font-size:.85rem;color:#4f46e5;margin-bottom:8px;">
                        <i class="bi bi-currency-dollar me-1"></i>Highest Revenue Generator
                    </div>
                    <?php if ($bestRevenue): ?>
                        <div style="font-size:.85rem;">
                            💰 <?= htmlspecialchars($bestRevenue['staff_name']) ?><br>
                            <span class="text-muted">₱<?= number_format($bestRevenue['total_revenue'], 2) ?> total</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);">
                    <div style="font-weight:700;font-size:.85rem;color:#d97706;margin-bottom:8px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Needs Improvement
                    </div>
                    <?php foreach ($lowActivity as $la): ?>
                        <div style="font-size:.85rem;">⚠️ <?= htmlspecialchars($la['staff_name']) ?> — <?= $la['total_orders'] ?> orders</div>
                    <?php endforeach; ?>
                    <?php if (empty($lowActivity)): ?>
                        <div class="text-muted" style="font-size:.82rem;">All staff are performing well!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>