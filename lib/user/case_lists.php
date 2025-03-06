<?php

require_once dirname(dirname(__DIR__)) . '/lib/dhu.php';
//require_once dirname(dirname(__DIR__)) . '/lib/db.php';

function getUserCases($status = null) {
    try {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return [];
        }

        $pdo = getDBConnection();
        $params = ['user_id' => $user_id];
        $sql = "SELECT * FROM cases WHERE user_id = :user_id";
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching cases: " . $e->getMessage());
        return [];
    }
}

function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'pending';
        case 'finalized':
            return 'completed';
        case 'awaiting':
            return 'urgent';
        default:
            return 'normal';
    }
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Add this new function
function get_case($case_id, $user_id) {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT * FROM cases WHERE case_id = :case_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'case_id' => $case_id,
            'user_id' => $user_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching case details: " . $e->getMessage());
        return null;
    }
}