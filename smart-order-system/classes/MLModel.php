<?php
// classes/MLModel.php
// ML Model for Staff Performance Insight

require_once __DIR__ . '/Database.php';

class MLModel {

    private $db;

    private $weightOrderCount  = 0.40;
    private $weightRevenue     = 0.35;
    private $weightCompletion  = 0.25;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function preprocessData() {
        $sql = "
            SELECT
                u.id                                        AS staff_id,
                u.full_name                                 AS staff_name,
                u.role,
                COUNT(o.id)                                 AS total_orders,
                COALESCE(SUM(o.price * o.quantity), 0)      AS total_revenue,
                SUM(CASE WHEN o.status = 'completed'  THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN o.status = 'cancelled'  THEN 1 ELSE 0 END) AS cancelled_orders,
                SUM(CASE WHEN o.status = 'pending'    THEN 1 ELSE 0 END) AS pending_orders,
                SUM(CASE WHEN o.status = 'processing' THEN 1 ELSE 0 END) AS processing_orders,
                MIN(o.created_at)                           AS first_order_date,
                MAX(o.created_at)                           AS last_order_date
            FROM users u
            LEFT JOIN orders o ON o.created_by = u.id
            GROUP BY u.id, u.full_name, u.role
            ORDER BY total_orders DESC
        ";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            if ($row['total_orders'] > 0) {
                $row['completion_rate'] = round(($row['completed_orders'] / $row['total_orders']) * 100, 1);
            } else {
                $row['completion_rate'] = 0;
            }
        }
        unset($row);

        return $rows;
    }

    public function trainModel($dataset) {
        if (empty($dataset)) return array();

        $allOrders  = array();
        $allRevenue = array();
        foreach ($dataset as $row) {
            $allOrders[]  = $row['total_orders'];
            $allRevenue[] = $row['total_revenue'];
        }

        $maxOrders  = max($allOrders)  > 0 ? max($allOrders)  : 1;
        $maxRevenue = max($allRevenue) > 0 ? max($allRevenue) : 1;

        foreach ($dataset as &$row) {
            $normOrders     = $row['total_orders']    / $maxOrders;
            $normRevenue    = $row['total_revenue']   / $maxRevenue;
            $normCompletion = $row['completion_rate'] / 100;

            $row['ml_score'] = round(
                ($normOrders     * $this->weightOrderCount) +
                ($normRevenue    * $this->weightRevenue)    +
                ($normCompletion * $this->weightCompletion),
                4
            );
        }
        unset($row);

        usort($dataset, function($a, $b) {
            if ($b['ml_score'] == $a['ml_score']) return 0;
            return ($b['ml_score'] > $a['ml_score']) ? 1 : -1;
        });

        return $dataset;
    }

    public function predict($scoredDataset) {
        $total = count($scoredDataset);
        if ($total === 0) return array();

        foreach ($scoredDataset as $rank => &$row) {
            $percentile = ($total > 1) ? (1 - ($rank / ($total - 1))) : 1;

            if ($percentile >= 0.66 || $row['ml_score'] >= 0.65) {
                $row['performance_label'] = 'Top Performer';
                $row['label_color']       = 'success';
                $row['label_icon']        = 'bi-trophy-fill';
            } elseif ($percentile >= 0.33 || $row['ml_score'] >= 0.30) {
                $row['performance_label'] = 'Active';
                $row['label_color']       = 'primary';
                $row['label_icon']        = 'bi-person-check-fill';
            } else {
                $row['performance_label'] = 'Low Activity';
                $row['label_color']       = 'warning';
                $row['label_icon']        = 'bi-person-dash-fill';
            }

            $row['rank'] = $rank + 1;
        }
        unset($row);

        return $scoredDataset;
    }

    public function analyze() {
        $raw    = $this->preprocessData();
        $scored = $this->trainModel($raw);
        return   $this->predict($scored);
    }

    public function getSummary($results) {
        if (empty($results)) {
            return array(
                'top_performer'  => null,
                'total_staff'    => 0,
                'avg_completion' => 0,
                'total_orders'   => 0
            );
        }

        $totalOrders     = 0;
        $totalCompletion = 0;
        foreach ($results as $r) {
            $totalOrders     += $r['total_orders'];
            $totalCompletion += $r['completion_rate'];
        }

        return array(
            'top_performer'  => $results[0]['staff_name'],
            'top_score'      => $results[0]['ml_score'],
            'total_staff'    => count($results),
            'avg_completion' => round($totalCompletion / count($results), 1),
            'total_orders'   => $totalOrders,
        );
    }
}