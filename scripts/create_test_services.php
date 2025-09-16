<?php
/**
 * 创建100个测试服务用于分页测试
 * 用法: php scripts/create_test_services.php
 */

// 定义基本路径
define('APPROOT', dirname(__DIR__));

// 引入配置文件
require_once APPROOT . '/config/config.php';
require_once APPROOT . '/app/helpers/session_helper.php';

// 连接数据库
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "成功连接到数据库。\n";
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage() . "\n");
}

// 测试服务数据
$test_services = [
    // SEO服务
    [
        'title' => '专业SEO优化服务',
        'description' => '提供全面的网站SEO优化服务，包括关键词研究、内容优化、技术SEO等。帮助您的网站在搜索引擎中获得更好的排名。',
        'price' => 299.99,
        'delivery_time' => 7,
        'link_type' => 'follow',
        'duration' => 30,
        'category' => 'guest_post',
    ],
    [
        'title' => '高质量外链建设',
        'description' => '提供高质量的外链建设服务，通过权威网站的反向链接提升您的网站权重和搜索引擎排名。',
        'price' => 199.99,
        'delivery_time' => 5,
        'link_type' => 'follow',
        'duration' => 60,
        'category' => 'backlink',
    ],
    [
        'title' => '内容营销推广',
        'description' => '专业的内容营销服务，包括文章写作、博客发布、社交媒体推广等，帮助提升品牌知名度和网站流量。',
        'price' => 399.99,
        'delivery_time' => 10,
        'link_type' => 'nofollow',
        'duration' => 90,
        'category' => 'guest_post',
    ],
    [
        'title' => '社交媒体优化',
        'description' => '社交媒体账号优化和管理服务，提升品牌在社交平台上的影响力和用户参与度。',
        'price' => 149.99,
        'delivery_time' => 3,
        'link_type' => 'nofollow',
        'duration' => 30,
        'category' => 'backlink',
    ],
    [
        'title' => '网站速度优化',
        'description' => '专业的网站性能优化服务，包括代码优化、图片压缩、缓存设置等，提升网站加载速度和用户体验。',
        'price' => 249.99,
        'delivery_time' => 5,
        'link_type' => 'follow',
        'duration' => 45,
        'category' => 'guest_post',
    ],
];

// 获取所有用户ID
$users_query = "SELECT id FROM users WHERE status = 1 LIMIT 10";
$users_stmt = $pdo->query($users_query);
$users = $users_stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($users)) {
    die("没有找到可用的用户。请先创建一些用户。\n");
}

// 获取所有行业ID
$industries_query = "SELECT id FROM industries";
$industries_stmt = $pdo->query($industries_query);
$industries = $industries_stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($industries)) {
    die("没有找到可用的行业。请先创建一些行业。\n");
}

echo "开始创建测试服务...\n";

$created_count = 0;

// 创建100个测试服务
for ($i = 1; $i <= 100; $i++) {
    // 随机选择一个服务模板
    $template = $test_services[array_rand($test_services)];

    // 随机选择用户和行业
    $user_id = $users[array_rand($users)];
    $industry_id = $industries[array_rand($industries)];

    // 生成变体标题
    $variants = [
        '专业', '高级', '优质', '快速', '高效', '经济', '精品', '定制', '企业级', '个人化'
    ];
    $variants2 = [
        '服务', '方案', '套餐', '优化', '推广', '建设', '管理', '运营', '咨询', '实施'
    ];

    $variant = $variants[array_rand($variants)];
    $variant2 = $variants2[array_rand($variants2)];
    $title = $variant . $template['title'] . " - " . $variant2;

    // 生成变体描述
    $descriptions = [
        "我们的团队拥有丰富的经验，为您提供最优质的服务。",
        "采用最新的技术和方法，确保最佳效果。",
        "量身定制的解决方案，满足您的具体需求。",
        "专业的团队，卓越的服务，让您的业务更上一层楼。",
        "经过验证的有效方法，帮助您实现业务目标。",
        "性价比极高的服务，为您的投资带来最大回报。",
        "快速响应，专业服务，让您省心省力。",
        "创新的解决方案，为您的业务带来新的机遇。",
        "细致入微的服务，确保每个细节都完美无缺。",
        "结果导向的服务，以实际效果说话。"
    ];

    $extra_desc = $descriptions[array_rand($descriptions)];
    $description = $template['description'] . " " . $extra_desc;

    // 随机价格变化
    $price_multiplier = 0.8 + (mt_rand() / mt_getrandmax()) * 0.4; // 0.8 to 1.2
    $price = round($template['price'] * $price_multiplier, 2);

    // 随机交货时间变化
    $delivery_time = max(1, $template['delivery_time'] + mt_rand(-2, 3));

    // 随机持续时间变化
    $duration = max(15, $template['duration'] + mt_rand(-10, 20));

    // 插入数据库
    $sql = "INSERT INTO services (user_id, title, description, site_url, price, delivery_time, link_type, service_category, industry_id, is_adult_allowed, is_new_window, duration, status, created_at)
            VALUES (:user_id, :title, :description, :site_url, :price, :delivery_time, :link_type, :service_category, :industry_id, 0, 1, :duration, 1, NOW())";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $user_id,
        ':title' => $title,
        ':description' => $description,
        ':site_url' => 'https://example' . $i . '.com',
        ':price' => $price,
        ':delivery_time' => $delivery_time,
        ':link_type' => $template['link_type'],
        ':service_category' => $template['category'],
        ':industry_id' => $industry_id,
        ':duration' => $duration,
    ]);

    $created_count++;

    if ($created_count % 10 == 0) {
        echo "已创建 $created_count 个服务...\n";
    }
}

echo "\n完成！总共创建了 $created_count 个测试服务。\n";
echo "现在您可以访问服务列表页面来测试分页功能。\n";
?>