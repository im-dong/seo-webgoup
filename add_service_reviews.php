<?php
/*
 * Script to add reviews for specific services (ID: 4 and 6)
 * 为特定服务（ID: 4 和 6）添加好评
 */

require_once 'config/config.php';
require_once 'app/core/Database.php';

class ServiceReviewSeeder {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 获取现有的用户ID列表
    public function getExistingUserIds($limit = 50) {
        $this->db->query("SELECT id, username, email FROM users WHERE id != 1 ORDER BY RAND() LIMIT :limit");
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // 检查服务是否存在
    public function serviceExists($serviceId) {
        $this->db->query("SELECT id, user_id as seller_id, title FROM services WHERE id = :service_id");
        $this->db->bind(':service_id', $serviceId);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    // 为指定服务添加好评
    public function addReviewsForService($serviceId, $reviewCount) {
        $service = $this->serviceExists($serviceId);
        if (!$service) {
            echo "Service ID $serviceId not found!\n";
            return false;
        }

        echo "Adding $reviewCount positive reviews for service: {$service->title} (ID: $serviceId)...\n";

        // 服务相关的好评模板
        $reviewTemplates = [
            // 通用高质量好评
            "Excellent service! The results exceeded my expectations. Highly recommended!",
            "Outstanding quality and professional service. Will definitely use again!",
            "Amazing experience! The service was delivered on time and with exceptional quality.",
            "Professional, reliable, and delivers outstanding results. Very satisfied!",
            "Top-notch service! The attention to detail and quality is impressive.",
            "Exceptional service provider! Very professional and delivers great results.",
            "Highly recommended! The service quality is outstanding and worth every penny.",
            "Fantastic service! Quick turnaround and excellent communication throughout.",
            "Professional and efficient! The service exceeded all my expectations.",
            "Outstanding experience! From start to finish, everything was perfect.",

            // SEO相关好评
            "Incredible SEO service! My website rankings improved significantly within weeks.",
            "Expert SEO work! The backlinks provided are high quality and really effective.",
            "Game-changing SEO service! My organic traffic has increased dramatically.",
            "Professional SEO expertise! They know exactly what they're doing and deliver results.",
            "Exceptional SEO results! My website is now ranking on the first page for key terms.",
            "SEO masters! The optimization work has transformed my online presence.",
            "Outstanding SEO service! The return on investment has been incredible.",
            "Expert SEO strategies! My website authority has increased substantially.",
            "Professional SEO delivery! The results speak for themselves - highly recommended!",
            "Top-tier SEO service! The improvement in search rankings has been remarkable.",

            // 快速效果好评
            "Fast and effective! Saw noticeable improvements much quicker than expected.",
            "Quick delivery with outstanding quality. Very impressed with the results!",
            "Rapid results! The service was completed quickly and exceeded expectations.",
            "Efficient and professional! Fast turnaround without compromising on quality.",
            "Speedy service! Delivered ahead of schedule with exceptional quality.",
            "Quick and reliable! The service was completed fast and to a high standard.",
            "Impressive turnaround! Fast service without sacrificing quality or attention to detail.",
            "Efficient excellence! Quick delivery and outstanding results - highly recommended!",
            "Rapid quality! Delivered faster than promised with exceptional results.",
            "Quick professional service! The speed and quality combined is impressive.",

            // 客户服务好评
            "Excellent communication throughout! They kept me informed at every step.",
            "Outstanding customer service! Very responsive and helpful throughout the process.",
            "Professional and courteous! Great communication and service delivery.",
            "Exceptional client care! They went above and beyond to ensure satisfaction.",
            "Great communication! Regular updates and always available for questions.",
            "Customer-focused service! They truly care about delivering the best results.",
            "Professional interactions! Excellent communication and service quality.",
            "Outstanding support! Very helpful and responsive to all my questions.",
            "Client-centric approach! They prioritize customer satisfaction above all.",
            "Exceptional service experience! Great communication and professional delivery.",

            // 价值好评
            "Great value for money! The results far exceeded the investment.",
            "Worth every penny! Exceptional quality and results at a fair price.",
            "Excellent ROI! The service has paid for itself many times over.",
            "Fair pricing for outstanding quality! Highly recommended value.",
            "Great investment! The service quality and results justify the cost completely.",
            "Value for money! Professional service that delivers exceptional results.",
            "Affordable excellence! Great pricing for such high-quality service delivery.",
            "Reasonable rates! The quality and results make this service a bargain.",
            "Fair and transparent! No hidden fees, just great service at a fair price.",
            "Excellent value! Professional service that delivers outstanding ROI.",

            // 专业性好评
            "Expert knowledge and skills! True professionals in their field.",
            "Professional excellence! The level of expertise is impressive.",
            "Industry experts! They really know their stuff and deliver exceptional results.",
            "Professional mastery! The quality of work demonstrates deep expertise.",
            "Knowledgeable and skilled! Expert service from experienced professionals.",
            "Industry leaders! The level of professionalism and expertise is outstanding.",
            "Expert service! They clearly know what they're doing and do it well.",
            "Professional expertise! The quality of work shows their deep knowledge.",
            "Skilled professionals! Exceptional service delivery from true experts.",
            "Industry professionals! The level of expertise and quality is impressive.",

            // 长期效果好评
            "Long-lasting results! The benefits continue to pay off over time.",
            "Sustainable success! The results have been consistent and long-term.",
            "Enduring quality! The improvements have lasted and continue to deliver value.",
            "Long-term benefits! The service continues to provide value months later.",
            "Lasting impact! The results have been sustainable and continue to improve.",
            "Sustained excellence! The quality and results have remained consistent.",
            "Long-term success! The service continues to deliver outstanding results.",
            "Enduring value! The benefits have continued long after service completion.",
            "Sustainable results! The improvements have been lasting and effective.",
            "Long-lasting quality! The service continues to deliver exceptional value."
        ];

        // 获取现有用户
        $existingUsers = $this->getExistingUserIds();
        if (empty($existingUsers)) {
            echo "No existing users found! Please create some users first.\n";
            return false;
        }

        $createdReviews = 0;
        $reviewTemplateCount = count($reviewTemplates);

        for ($i = 0; $i < $reviewCount; $i++) {
            // 随机选择一个现有用户
            $buyer = $existingUsers[array_rand($existingUsers)];
            $buyerId = $buyer->id;

            // 直接添加评价，不创建订单，使用虚拟订单ID
            $reviewText = $reviewTemplates[array_rand($reviewTemplates)];
            $rating = rand(4, 5); // 4-5星好评
            $reviewDate = date('Y-m-d H:i:s', strtotime("-" . rand(1, 90) . " days"));
            $fakeOrderId = $serviceId * 1000 + $i + 1; // 生成唯一的虚拟订单ID

            try {
                $this->db->query("INSERT INTO reviews (order_id, reviewer_id, seller_id, rating, comment, created_at)
                                 VALUES (:order_id, :reviewer_id, :seller_id, :rating, :comment, :created_at)");
                $this->db->bind(':order_id', $fakeOrderId);
                $this->db->bind(':reviewer_id', $buyerId);
                $this->db->bind(':seller_id', $service->seller_id);
                $this->db->bind(':rating', $rating);
                $this->db->bind(':comment', $reviewText);
                $this->db->bind(':created_at', $reviewDate);

                if ($this->db->execute()) {
                    $createdReviews++;
                    echo "Review $createdReviews/$reviewCount created by {$buyer->username}\n";
                }
            } catch (Exception $e) {
                // 如果失败，跳过继续下一个
                continue;
            }

            // 避免过多重复用户
            if ($createdReviews >= count($existingUsers) * 0.8) {
                break;
            }
        }

        echo "\nTotal reviews created for service $serviceId: $createdReviews\n";
        return $createdReviews;
    }
}

// 运行评价脚本
$seeder = new ServiceReviewSeeder();

// 为服务4添加40-60个好评
$service4Reviews = rand(40, 60);
echo "=== Adding reviews for Service ID 4 ===\n";
$result4 = $seeder->addReviewsForService(4, $service4Reviews);

// 为服务6添加40-60个好评
$service6Reviews = rand(40, 60);
echo "\n=== Adding reviews for Service ID 6 ===\n";
$result6 = $seeder->addReviewsForService(6, $service6Reviews);

echo "\n=== Summary ===\n";
echo "Service 4: $result4 reviews added\n";
echo "Service 6: $result6 reviews added\n";
echo "Total: " . ($result4 + $result6) . " reviews added\n";

echo "\nReview addition completed successfully!\n";
?>