<?php
/*
 * 更新现有评价数据，添加service_id字段
 * 根据虚拟订单ID推断对应的服务ID
 */

require_once 'config/config.php';
require_once 'app/core/Database.php';

class UpdateReviewsServiceId {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateExistingReviews() {
        echo "Updating existing reviews with service_id...\n";

        // 获取所有没有service_id的评价
        $this->db->query("SELECT r.id, r.order_id, r.comment
                         FROM reviews r
                         WHERE r.service_id IS NULL
                         ORDER BY r.id");
        $reviews = $this->db->resultSet();

        $updatedCount = 0;
        $totalReviews = count($reviews);

        echo "Found $totalReviews reviews without service_id\n";

        foreach ($reviews as $review) {
            // 从虚拟订单ID推断服务ID
            // 我们的虚拟订单ID格式: serviceId * 1000 + index + 1
            $potentialServiceId = floor(($review->order_id - 1) / 1000);

            // 验证这个服务ID是否存在
            $this->db->query("SELECT id FROM services WHERE id = :service_id");
            $this->db->bind(':service_id', $potentialServiceId);
            $service = $this->db->single();

            if ($service) {
                // 更新评价的service_id
                $this->db->query("UPDATE reviews SET service_id = :service_id WHERE id = :review_id");
                $this->db->bind(':service_id', $potentialServiceId);
                $this->db->bind(':review_id', $review->id);

                if ($this->db->execute()) {
                    $updatedCount++;
                    echo "Updated review {$review->id}: order_id {$review->order_id} -> service_id {$potentialServiceId}\n";
                }
            } else {
                echo "Could not find service for review {$review->id} with order_id {$review->order_id} (inferred service_id: {$potentialServiceId})\n";
            }
        }

        echo "\nUpdate completed:\n";
        echo "Total reviews processed: $totalReviews\n";
        echo "Successfully updated: $updatedCount\n";
        echo "Failed to update: " . ($totalReviews - $updatedCount) . "\n";

        return $updatedCount;
    }
}

// 执行更新
$updater = new UpdateReviewsServiceId();
$updater->updateExistingReviews();

echo "\nScript completed!\n";
?>