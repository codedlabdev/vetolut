<?php
require_once BASE_DIR . '/lib/dhu.php';

/**
 * Get all active course categories
 *
 * @return array Array of categories
 */
function getCourseCategories() {
    // Get database connection
    $conn = getDBConnection();

    try {
        $stmt = $conn->prepare("SELECT id, name FROM course_categories WHERE status = 1 ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching course categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new course
 *
 * @param array $courseData Course data including title, description, category_id, etc.
 * @param int $userId ID of the user creating the course
 * @return array Response with success status and message
 */
function createCourse($courseData, $userId) {
    $conn = getDBConnection();

    try {
        // Start transaction
        $conn->beginTransaction();

        // Validate required fields
        if (empty($courseData['title']) || empty($courseData['description']) || empty($courseData['category_id'])) {
            throw new Exception('Missing required fields');
        }

        // Debugging: Log course data
        error_log('Course Data: ' . print_r($courseData, true));

        $bannerImage = null;
        $introVideo = null;

        // Handle banner image upload
        if (isset($courseData['preview_image']) && $courseData['preview_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_DIR . 'public/uploads/courses/preview-img/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageFileType = strtolower(pathinfo($courseData['preview_image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('course_img_') . '.' . $imageFileType;
            $targetFile = $uploadDir . $newFileName;

            // Validate image file
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF');
            }

            if (move_uploaded_file($courseData['preview_image']['tmp_name'], $targetFile)) {
                $bannerImage = 'uploads/courses/preview-img/' . $newFileName;
            } else {
                throw new Exception('Failed to upload image');
            }
        }

        // Handle intro video upload
        if (isset($courseData['preview_video']) && $courseData['preview_video']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_DIR . 'public/uploads/courses/preview-video/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Check video file size (max 100MB)
            $maxFileSize = 100 * 1024 * 1024; // 100MB in bytes
            if ($courseData['preview_video']['size'] > $maxFileSize) {
                throw new Exception('Video file size must be less than 100MB');
            }

            $videoFileType = strtolower(pathinfo($courseData['preview_video']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('course_video_') . '.' . $videoFileType;
            $targetFile = $uploadDir . $newFileName;

            // Validate video file type
            if (!in_array($videoFileType, ['mp4', 'webm', 'ogg'])) {
                throw new Exception('Invalid video format. Allowed formats: MP4, WEBM, OGG');
            }

            if (move_uploaded_file($courseData['preview_video']['tmp_name'], $targetFile)) {
                $introVideo = 'uploads/courses/preview-video/' . $newFileName;
            } else {
                throw new Exception('Failed to upload video');
            }
        }

        // Prepare the SQL statement with price and status
        $sql = "INSERT INTO courses (user_id, category_id, title, description, banner_image, intro_video, price, status, duration_hr, duration_min, created_at, updated_at) 
                VALUES (:user_id, :category_id, :title, :description, :banner_image, :intro_video, :price, :status, :duration_hr, :duration_min, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':category_id' => $courseData['category_id'],
            ':title' => $courseData['title'],
            ':description' => $courseData['description'],
            ':banner_image' => $bannerImage,
            ':intro_video' => $introVideo,
            ':price' => $courseData['price'] ?? 0.00,
            ':status' => $courseData['status'] ?? 'draft',
            ':duration_hr' => $courseData['duration_hr'] ?? 0,
            ':duration_min' => $courseData['duration_min'] ?? 0
        ]);

        $courseId = $conn->lastInsertId();

        // Commit transaction
        $conn->commit();

        return [
            'success' => true,
            'message' => 'Course created successfully',
            'course_id' => $courseId
        ];

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        error_log("Error creating course: " . $e->getMessage());

        return [
            'success' => false,
            'message' => 'Error creating course: ' . $e->getMessage()
        ];
    }
}

/**
 * Get course by ID
 *
 * @param int $courseId Course ID
 * @param int $userId Optional user ID to check ownership
 * @return array|false Course data or false if not found
 */
function getCourse($courseId, $userId = null) {
    $conn = getDBConnection();

    try {
        $sql = "SELECT * FROM courses WHERE id = :id";
        $params = [':id' => $courseId];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching course: " . $e->getMessage());
        return false;
    }
}

/**
 * Update course details
 *
 * @param int $courseId Course ID
 * @param array $courseData Updated course data
 * @param int $userId User ID for ownership verification
 * @return array Response with success status and message
 */
function updateCourse($courseId, $courseData, $userId) {
    $conn = getDBConnection();

    try {
        // Start transaction
        $conn->beginTransaction();

        // First verify ownership and get current course data
        $stmt = $conn->prepare("SELECT * FROM courses WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $courseId, ':user_id' => $userId]);
        $currentCourse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentCourse) {
            throw new Exception('Course not found or you don\'t have permission to edit it');
        }

        $bannerImage = null;
        $introVideo = null;

        // Handle banner image upload
        if (isset($courseData['preview_image']) && $courseData['preview_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_DIR . 'public/uploads/courses/preview-img/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Delete old image if exists
            if (!empty($currentCourse['banner_image'])) {
                $oldImagePath = BASE_DIR . 'public/' . $currentCourse['banner_image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageFileType = strtolower(pathinfo($courseData['preview_image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('course_img_') . '.' . $imageFileType;
            $targetFile = $uploadDir . $newFileName;

            // Validate image file
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF');
            }

            if (move_uploaded_file($courseData['preview_image']['tmp_name'], $targetFile)) {
                $bannerImage = 'uploads/courses/preview-img/' . $newFileName;
            } else {
                throw new Exception('Failed to upload image');
            }
        }

        // Handle intro video upload
        if (isset($courseData['preview_video']) && $courseData['preview_video']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_DIR . 'public/uploads/courses/preview-video/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Delete old video if exists
            if (!empty($currentCourse['intro_video'])) {
                $oldVideoPath = BASE_DIR . 'public/' . $currentCourse['intro_video'];
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }

            // Check video file size (max 100MB)
            $maxFileSize = 100 * 1024 * 1024; // 100MB in bytes
            if ($courseData['preview_video']['size'] > $maxFileSize) {
                throw new Exception('Video file size must be less than 100MB');
            }

            $videoFileType = strtolower(pathinfo($courseData['preview_video']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('course_video_') . '.' . $videoFileType;
            $targetFile = $uploadDir . $newFileName;

            // Validate video file type
            if (!in_array($videoFileType, ['mp4', 'webm', 'ogg'])) {
                throw new Exception('Invalid video format. Allowed formats: MP4, WEBM, OGG');
            }

            if (move_uploaded_file($courseData['preview_video']['tmp_name'], $targetFile)) {
                $introVideo = 'uploads/courses/preview-video/' . $newFileName;
            } else {
                throw new Exception('Failed to upload video');
            }
        }

        // Build update query dynamically based on what's being updated
        $updateFields = [
            'title = :title',
            'description = :description',
            'category_id = :category_id',
            'status = :status',
            'price = :price',
            'duration_hr = :duration_hr',
            'duration_min = :duration_min',
            'updated_at = NOW()'
        ];

        $params = [
            ':id' => $courseId,
            ':title' => $courseData['title'],
            ':description' => $courseData['description'],
            ':category_id' => $courseData['category_id'],
            ':status' => $courseData['status'],
            ':price' => $courseData['price'],
            ':duration_hr' => $courseData['duration_hr'],
            ':duration_min' => $courseData['duration_min']
        ];

        // Add banner_image if uploaded
        if ($bannerImage) {
            $updateFields[] = 'banner_image = :banner_image';
            $params[':banner_image'] = $bannerImage;
        }

        // Add intro_video if uploaded
        if ($introVideo) {
            $updateFields[] = 'intro_video = :intro_video';
            $params[':intro_video'] = $introVideo;
        }

        $sql = "UPDATE courses SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        // Commit transaction
        $conn->commit();

        return [
            'success' => true,
            'message' => 'Course updated successfully'
        ];

    } catch(Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error updating course: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Delete a course
 *
 * @param int $courseId Course ID
 * @param int $userId User ID for ownership verification
 * @return bool True if deleted successfully, false otherwise
 */
function deleteCourse($courseId, $userId) {
    try {
        $conn = getDBConnection();
        $conn->beginTransaction();

        // Delete course record
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = :id AND user_id = :user_id");
        $success = $stmt->execute([':id' => $courseId, ':user_id' => $userId]);

        if ($success) {
            // Add logic to delete associated files if needed
            $conn->commit();
            return true;
        } else {
            throw new Exception('Failed to delete course');
        }
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error deleting course: " . $e->getMessage());
        return false;
    }
}

/**
 * Get course topics
 *
 * @param int $courseId Course ID
 * @return array Array of topics
 */
function getCourseSections($courseId) {
    $conn = getDBConnection();
    
    try {
        $stmt = $conn->prepare("SELECT * FROM course_sections WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching course sections: " . $e->getMessage());
        return [];
    }
}

/**
 * Add a new section to a course
 *
 * @param int $courseId Course ID
 * @param array $sectionData Section data including title, description, etc.
 * @return array Response with success status and message
 */
function addCourseSection($courseId, $sectionData) {
    $conn = getDBConnection();
    
    try {
        // Get the current maximum position for this course
        $positionSql = "SELECT COALESCE(MAX(position), 0) as max_position FROM course_sections WHERE course_id = ?";
        $positionStmt = $conn->prepare($positionSql);
        $positionStmt->execute([$courseId]);
        $row = $positionStmt->fetch(PDO::FETCH_ASSOC);
        $newPosition = $row['max_position'] + 1;
        
        // Prepare the SQL statement
        $sql = "INSERT INTO course_sections (course_id, title, description, video_url, position) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        // Execute with parameters
        $stmt->execute([
            $courseId,
            $sectionData['title'],
            $sectionData['description'],
            $sectionData['video_url'],
            $newPosition
        ]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Section added successfully',
                'section_id' => $conn->lastInsertId()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add section'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * Update a course section
 *
 * @param int $sectionId Section ID
 * @param array $sectionData Updated section data
 * @return array Response with success status and message
 */
function updateCourseSection($sectionId, $data) {
    $conn = getDBConnection();
    
    try {
        $updateFields = [];
        $params = [':id' => $sectionId];

        if (isset($data['title'])) {
            $updateFields[] = 'title = :title';
            $params[':title'] = $data['title'];
        }

        if (isset($data['description'])) {
            $updateFields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }

        if (isset($data['video_url'])) {
            $updateFields[] = 'video_url = :video_url';
            $params[':video_url'] = $data['video_url'];
        }

        // Changed from $setFields to $updateFields in the SQL query
        $sql = "UPDATE course_sections SET " . implode(', ', $updateFields) . " WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Failed to prepare statement");
            return false;
        }

        $result = $stmt->execute($params);
        
        if (!$result) {
            error_log("Failed to execute statement: " . implode(', ', $stmt->errorInfo()));
            return false;
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error updating course section: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a course section
 *
 * @param int $sectionId Section ID
 * @return array Response with success status and message
 */
function deleteCourseSection($sectionId) {
    $conn = getDBConnection();
    
    try {
        // First get the video URL before deleting the section
        $stmt = $conn->prepare("SELECT video_url FROM course_sections WHERE id = :id");
        $stmt->execute([':id' => $sectionId]);
        $section = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($section && !empty($section['video_url'])) {
            // Construct full path to video file
            $videoPath = BASE_DIR . 'public/' . $section['video_url'];
            
            // Delete video file if it exists
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
        }
        
        // Now delete the section from database
        $stmt = $conn->prepare("DELETE FROM course_sections WHERE id = :id");
        $stmt->execute([':id' => $sectionId]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error deleting course section: " . $e->getMessage());
        return false;
    }
}

/**
 * Update course section positions
 *
 * @param array $positions Array of section IDs and their new positions
 * @return array Response with success status and message
 */
function updateCourseSectionPositions($positions) {
    $conn = getDBConnection();

    // Implementation here
}

/**
 * Get all courses for a specific user
 *
 * @param int $userId ID of the user
 * @return array Array of courses
 */
function getUserCourses($userId) {
    $conn = getDBConnection();

    try {
        $stmt = $conn->prepare("
            SELECT c.*, cc.name as category_name 
            FROM courses c 
            LEFT JOIN course_categories cc ON c.category_id = cc.id 
            WHERE c.user_id = :user_id 
            ORDER BY c.created_at DESC
        ");
        
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching user courses: " . $e->getMessage());
        return [];
    }
}
/**
 * Get published courses with pagination
 *
 * @param int $limit Number of items per page
 * @param int $offset Starting position
 * @return array Array of courses and total count
 */
function getPublishedCourses($limit, $offset) {
    $conn = getDBConnection();

    try {
        // Debug: Log connection status
        error_log("Database connection established");

        // Get total count first
        $countStmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM courses c
            WHERE c.status = 'published' 
            AND c.admin_status = 1
        ");
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Debug: Log total count
        error_log("Total published courses found: " . $total);

        // Get courses with pagination
        $stmt = $conn->prepare("
            SELECT c.*, u.f_name, u.l_name, cc.name as category_name
            FROM courses c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN course_categories cc ON c.category_id = cc.id
            WHERE c.status = 'published' 
            AND c.admin_status = 1
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        
        // Debug: Log query parameters
        error_log("Query parameters - Limit: $limit, Offset: $offset");

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Log results
        error_log("Courses fetched: " . count($courses));

        // Format instructor name
        foreach ($courses as &$course) {
            $course['instructor_name'] = $course['f_name'] . ' ' . $course['l_name'];
        }

        return [
            'courses' => $courses,
            'total' => $total
        ];
    } catch(PDOException $e) {
        error_log("Error fetching published courses: " . $e->getMessage());
        return [
            'courses' => [],
            'total' => 0
        ];
    }
}


/**
 * Retrieves detailed information about a specific course
 *
 * @param int $course_id The ID of the course to retrieve
 * @return array|null Returns an array containing course details or null if not found
 */
function getCourseDetails($course_id) {
    $conn = getDBConnection();
    
    try {
        $query = "SELECT 
            c.*,
            CONCAT(u.f_name, ' ', u.l_name) as instructor_name,
            u.image as instructor_image,
            u.profession as instructor_profession,
            cc.name as category_name
        FROM courses c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN course_categories cc ON c.category_id = cc.id
        WHERE c.id = :course_id 
        AND c.status = 'published' 
        AND c.admin_status = 1";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($course) {
            // Handle instructor image path
            if (!empty($course['instructor_image'])) {
                if (strpos($course['instructor_image'], 'http') === false) {
                    $course['instructor_image'] = BASE_URL . $course['instructor_image'];
                }
            }
            
            // Format price to ensure it's numeric
            $course['price'] = floatval($course['price']);
            
            return $course;
        }
        
        return null;

    } catch (PDOException $e) {
        error_log("Error fetching course details: " . $e->getMessage());
        return null;
    }
}