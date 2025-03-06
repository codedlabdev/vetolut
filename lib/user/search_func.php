<?php
 

function search_users($search_term) {
    global $pdo;
    
    $search_term = '%' . $search_term . '%';
    
    // SQL to search across name, email, and phone
    $sql = "SELECT id, f_name, l_name, email, phone, profession, image, country 
            FROM users 
            WHERE verify = 1 
            AND (
                f_name LIKE ? 
                OR l_name LIKE ? 
                OR email LIKE ? 
                OR phone LIKE ?
            )";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_term, $search_term, $search_term, $search_term]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Handle user images
        foreach ($results as &$user) {
            if (!empty($user['image'])) {
                if (strpos($user['image'], '/') !== false) {
                    // It's a file path
                    $user['image'] = BASE_URL . $user['image'];
                } else {
                    // It's a BLOB, use default image
                    $user['image'] = BASE_URL . 'assets/user/img/noimage.png';
                }
            } else {
                // If there's no image, use default placeholder
                $user['image'] = BASE_URL . 'assets/user/img/noimage.png';
            }
        }
        
        return $results;
    } catch (PDOException $e) {
        error_log("Search error: " . $e->getMessage());
        return [];
    }
}
 // SQL to search across Ai
function search_ai_queries($search_term) {
    global $pdo;
    
    $search_term = '%' . $search_term . '%';
    
    $sql = "SELECT ac.id, ac.chat_id, ac.user_id, ac.title, ac.prompt, ac.created_at,
                   u.f_name, u.l_name
            FROM ai_chat ac
            LEFT JOIN users u ON ac.user_id = u.id
            WHERE ac.title LIKE ?
            ORDER BY ac.created_at DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_term]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("AI Query search error: " . $e->getMessage());
        return [];
    }
}
