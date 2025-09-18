<?php
/*
 * Script to add 180 positive reviews for admin user
 * 为admin用户添加180条最高分好评
 */

require_once 'config/config.php';
require_once 'app/core/Database.php';

class ReviewSeeder {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAdminId() {
        $this->db->query("SELECT id FROM users WHERE username = 'admin'");
        $result = $this->db->single();
        return $result ? $result->id : null;
    }

    public function addReviews() {
        $adminId = $this->getAdminId();
        if (!$adminId) {
            echo "Admin user not found!\n";
            return false;
        }

        echo "Adding 180 positive reviews for admin (ID: $adminId)...\n";

        // Sample review templates
        $reviewTemplates = [
            // Ahrefs rating improvement reviews
            "Amazing service! My Ahrefs rating went from 15 to 45 in just 2 months. The DA 80 backlinks they provided are incredibly powerful. Highly recommend!",

            "Outstanding SEO work! My website's Ahrefs domain rating jumped from 25 to 55. The high-authority backlinks they built are exactly what I needed.",

            "Incredible results! Ahrefs rating improved from 18 to 48. The DA 70+ backlinks are genuine and have significantly boosted my search rankings.",

            "Best SEO investment I've made! Ahrefs DR went from 12 to 42. The quality of backlinks provided is exceptional.",

            "Professional and effective! My Ahrefs rating soared from 20 to 50. The DA 75-80 backlinks are worth every penny.",

            // DA 40 website transformation reviews
            "Transformed my completely new website to DA 40 in just 3 months! This is unbelievable service. They delivered exactly what they promised.",

            "From DA 0 to DA 40 in 90 days! My website is now ranking on first page for competitive keywords. Absolutely amazing service!",

            "These guys are magicians! Took my new site from no authority to DA 40. The organic traffic has increased 10x.",

            "Incredible achievement! My website went from DA 5 to DA 40. The comprehensive SEO strategy they implemented works wonders.",

            "Life-changing service! Transformed my website to DA 40. Now getting consistent organic traffic and sales.",

            // Gov backlink reviews
            "The .gov backlinks are pure gold! My website's authority skyrocketed after getting these high-authority government links.",

            "Authentic .gov backlinks that actually work! My search rankings improved dramatically within weeks.",

            "These gov links are the real deal! Website credibility has increased tremendously. Highly recommend this service.",

            "Powerful .edu and .gov backlinks! My domain authority jumped 15 points just from these links alone.",

            "Government backlinks at their finest! The quality and authenticity of these links are unmatched.",

            // High DA backlink reviews (DA 50-80)
            "The DA 65 backlinks they provided are incredibly powerful. My website is now ranking on page 1 for competitive terms.",

            "Outstanding DA 70+ backlinks! These links have significantly improved my website's authority and rankings.",

            "Quality DA 55-80 backlinks that actually deliver results. My organic traffic has increased by 300%.",

            "High-authority backlinks that work! The DA 60+ links have boosted my rankings faster than expected.",

            "Professional backlink service! The DA 50-80 range links they provide are genuine and effective.",

            // General SEO service reviews
            "Comprehensive SEO service that delivers real results! My website traffic has increased 5x since working with them.",

            "Exceptional SEO expertise! They understand exactly what websites need to rank higher in search results.",

            "Results-driven SEO service! My website is now getting consistent organic traffic and leads.",

            "Professional and reliable SEO service! They delivered everything they promised and more.",

            "Game-changing SEO service! My online business has grown tremendously thanks to their expertise.",

            // Specific service type reviews
            "Guest post service is top-notch! High-authority placements that actually drive traffic and improve rankings.",

            "Backlink building service exceeded expectations! Quality links from relevant websites in my niche.",

            "SEO audit and implementation was comprehensive! They identified issues I didn't even know existed.",

            "Content marketing service is outstanding! Well-researched content that ranks well and converts.",

            "Technical SEO service is worth every penny! Fixed all my website's technical issues and improved speed.",

            // Rapid improvement reviews
            "Incredibly fast results! Saw improvement in rankings within just 2 weeks of starting the service.",

            "Quick and effective SEO! My website started ranking on first page within a month.",

            "Rapid ranking improvements! Competing with established websites in my niche now.",

            "Fast-acting SEO strategies! Saw traffic increase within the first month of service.",

            "Quick turnaround on results! My website's visibility improved much faster than anticipated.",

            // Long-term results reviews
            "Sustainable SEO results! My rankings have remained stable and continue to improve over time.",

            "Long-term SEO success! Six months later and my website is still ranking strong.",

            "Consistent SEO performance! The results they achieved have lasted and continue to improve.",

            "Enduring SEO benefits! My website maintains high rankings even after service completion.",

            "Lasting SEO impact! The improvements they made continue to benefit my business daily.",

            // Customer service reviews
            "Excellent customer service! They kept me informed throughout the entire process.",

            "Professional communication! Always responsive and answered all my questions promptly.",

            "Great customer support! They went above and beyond to ensure I was satisfied with results.",

            "Transparent and honest service! No hidden fees or false promises.",

            "Outstanding client relationships! They truly care about their clients' success.",

            // Value for money reviews
            "Great value for money! The ROI on their SEO service is incredible.",

            "Worth every penny! The results I got far exceeded the investment.",

            "Affordable SEO that works! Best investment I've made for my online business.",

            "Reasonable pricing for exceptional results! Highly competitive in the market.",

            "Fair pricing structure! No surprises and great return on investment.",

            // Technical expertise reviews
            "Technical SEO experts! They solved complex issues that other agencies couldn't fix.",

            "Deep SEO knowledge! Their expertise shows in every aspect of their work.",

            "Advanced SEO strategies! Using cutting-edge techniques that actually work.",

            "Technical proficiency! They understand Google's algorithms and ranking factors.",

            "SEO masters! Their technical skills are unmatched in the industry.",

            // Local SEO reviews
            "Local SEO genius! My business now appears at the top of local search results.",

            "Google My Business optimization! My local visibility has improved dramatically.",

            "Local search dominance! Getting more calls and walk-ins from local customers.",

            "Geo-targeted SEO excellence! Perfect for businesses targeting local markets.",

            "Local SEO specialists! They understand how to optimize for specific geographic areas.",

            // Content quality reviews
            "Content creation wizards! The SEO-optimized content ranks well and engages readers.",
            "High-quality content that converts! Not just optimized for search, but for users too.",
            "Content marketing excellence! Well-researched articles that establish authority.",
            "SEO content specialists! They know how to create content that both Google and users love.",
            "Content strategy masters! The content they created continues to bring traffic months later.",

            // Analytics and reporting reviews
            "Detailed reporting and analytics! They provided clear insights into progress and results.",
            "Transparent reporting! I could see exactly how my website was improving.",
            "Comprehensive analytics! They track all important metrics and provide actionable insights.",
            "Clear progress reports! Regular updates showed steady improvement in rankings.",
            "Data-driven approach! They use analytics to guide their SEO strategies effectively.",

            // Competitive advantage reviews
            "Outranking competitors! My website now appears above established competitors.",
            "Competitive edge achieved! Their strategies helped me surpass competitors in search results.",
            "Market domination! My website now dominates search results in my industry.",
            "Competitor analysis excellence! They identified and exploited competitor weaknesses.",
            "Strategic competitive advantage! Their SEO approach gives me an edge over competitors.",

            // Trust and authority reviews
            "Building trust and authority! My website is now seen as an industry leader.",
            "Establishing credibility! The SEO work has made my brand more trustworthy online.",
            "Authority building masters! My website is now cited as a source in my industry.",
            "Brand recognition increased! The SEO efforts have improved my brand's online presence.",
            "Industry authority achieved! My website is now considered an authoritative source.",

            // Mobile optimization reviews
            "Mobile SEO experts! My website now ranks perfectly on mobile searches.",
            "Mobile-first optimization! They ensured my site performs well on all devices.",
            "Responsive design SEO! My mobile rankings have improved significantly.",
            "Mobile-friendly excellence! The mobile optimization has increased mobile traffic.",
            "Cross-device SEO mastery! My website ranks well on desktop, tablet, and mobile.",

            // Conversion optimization reviews
            "Conversion-focused SEO! Not just rankings, but actual business results.",
            "SEO that converts! The traffic they brought actually turns into customers.",
            "Business impact SEO! My conversion rate has increased along with rankings.",
            "Revenue-generating SEO! The improvements have directly increased my sales.",
            "ROI-driven SEO! Every optimization was focused on business outcomes.",

            // E-commerce specific reviews
            "E-commerce SEO specialists! My product pages now rank on first page.",
            "Product listing optimization! My online store traffic has increased 400%.",
            "E-commerce revenue growth! SEO has directly increased my online sales.",
            "Shopping SEO excellence! My products now appear in Google Shopping results.",
            "E-commerce visibility expert! My store is now easily found by potential customers.",

            // Recovery and penalty reviews
            "Google penalty recovery! They helped me recover from a manual penalty.",
            "Algorithm update recovery! My rankings bounced back after Google updates.",
            "SEO crisis management! They fixed issues that were causing ranking drops.",
            "Recovery specialists! Brought my website back from significant ranking losses.",
            "Penalty prevention! They identified and fixed issues before they became problems.",

            // International SEO reviews
            "International SEO expansion! My website now ranks in multiple countries.",
            "Global SEO strategy! They helped me expand my reach to international markets.",
            "Multi-language SEO! My website is now optimized for different languages.",
            "Global visibility achieved! My business now attracts international customers.",
            "Worldwide SEO success! My website ranks well across different regions.",

            // Specialized niche reviews
            "Niche SEO experts! They understand my specialized industry perfectly.",
            "Industry-specific SEO! Their knowledge of my niche is impressive.",
            "Specialized market mastery! They know exactly how to SEO for my industry.",
            "Niche domination! My website now leads search results in my specialized field.",
            "Industry-focused SEO! They understand the unique challenges of my market."
        ];

        // Generate random usernames
        $firstNames = [
            'Alex', 'Jordan', 'Taylor', 'Morgan', 'Casey', 'Riley', 'Avery', 'Quinn', 'Sage', 'Rowan',
            'Drew', 'Blake', 'Cameron', 'Dakota', 'Emerson', 'Finley', 'Gray', 'Harper', 'Indigo', 'Jade',
            'Kai', 'Lane', 'Maverick', 'Nova', 'Phoenix', 'River', 'Sky', 'Tatum', 'Uma', 'Violet',
            'Willow', 'Xander', 'Yara', 'Zane', 'Arlo', 'Briar', 'Cleo', 'Dex', 'Eira', 'Finn',
            'Gwen', 'Huxley', 'Iris', 'Jax', 'Kira', 'Leo', 'Mila', 'Niko', 'Orion', 'Piper',
            'Ronan', 'Sloane', 'Theo', 'Uma', 'Vex', 'Wren', 'Xyla', 'Yoshi', 'Zara', 'Asher',
            'Bellamy', 'Corbin', 'Dalia', 'Ellis', 'Flint', 'Gia', 'Heath', 'Ivy', 'Jett', 'Koa',
            'Luna', 'Marlowe', 'Nix', 'Onyx', 'Pax', 'Quinn', 'Rune', 'Sage', 'Thorne', 'Uriah',
            'Vesper', 'Wilde', 'Xander', 'Yarrow', 'Zephyr', 'Atlas', 'Blythe', 'Caspian', 'Darcy', 'Elowen'
        ];

        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
            'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
            'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell', 'Carter', 'Roberts',
            'Gomez', 'Phillips', 'Evans', 'Turner', 'Diaz', 'Parker', 'Cruz', 'Edwards', 'Collins', 'Reyes',
            'Stewart', 'Morris', 'Morales', 'Murphy', 'Cook', 'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Cooper',
            'Peterson', 'Bailey', 'Reed', 'Kelly', 'Howard', 'Ramos', 'Kim', 'Cox', 'Ward', 'Richardson',
            'Watson', 'Brooks', 'Chavez', 'Wood', 'James', 'Bennett', 'Gray', 'Mendoza', 'Ruiz', 'Hughes',
            'Price', 'Alvarez', 'Castillo', 'Sanders', 'Patel', 'Myers', 'Long', 'Ross', 'Foster', 'Jimenez'
        ];

        $userSuffixes = [
            '', '92', '88', '99', '77', '123', '2024', 'pro', 'seo', 'digital', 'web', 'tech', 'online', 'marketing'
        ];

        $createdReviews = 0;
        $reviewCount = count($reviewTemplates);

        for ($i = 0; $i < 180; $i++) {
            // Generate random username
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $suffix = $userSuffixes[array_rand($userSuffixes)];

            $buyerUsername = $firstName . $lastName . $suffix;
            $buyerEmail = strtolower($firstName . '.' . $lastName) . ($suffix ? $suffix : '') . '@' . rand(1, 9) . 'gmail.com';

            // Check if user exists, if not create it
            $this->db->query("SELECT id FROM users WHERE email = :email");
            $this->db->bind(':email', $buyerEmail);
            $existingUser = $this->db->single();

            if (!$existingUser) {
                $this->db->query("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $this->db->bind(':username', $buyerUsername);
                $this->db->bind(':email', $buyerEmail);
                $this->db->bind(':password', password_hash('password123', PASSWORD_DEFAULT));
                $this->db->execute();
                $buyerId = $this->db->lastInsertId();
            } else {
                $buyerId = $existingUser->id;
            }

            // Create a fake service for admin
            $serviceTitles = [
                "Premium DA 80 Backlink Package",
                "Complete Website DA 40 Transformation",
                "Government Authority Backlink Service",
                "Ahrefs Rating Boost Package",
                "High Authority SEO Service",
                "Premium Guest Post Service",
                "Comprehensive SEO Optimization",
                "Domain Authority Building Service",
                "Authority Backlink Building",
                "Professional SEO Consultation"
            ];

            $serviceTitle = $serviceTitles[array_rand($serviceTitles)];
            $servicePrice = rand(200, 1000);

            // Check if service exists, if not create it
            $this->db->query("SELECT id FROM services WHERE user_id = :user_id AND title = :title");
            $this->db->bind(':user_id', $adminId);
            $this->db->bind(':title', $serviceTitle);
            $existingService = $this->db->single();

            if (!$existingService) {
                $this->db->query("INSERT INTO services (user_id, title, description, price, status, service_category, delivery_time) VALUES (:user_id, :title, :description, :price, 1, 'backlink', 7)");
                $this->db->bind(':user_id', $adminId);
                $this->db->bind(':title', $serviceTitle);
                $this->db->bind(':description', "Premium SEO service that delivers outstanding results for your website.");
                $this->db->bind(':price', $servicePrice);
                $this->db->execute();
                $serviceId = $this->db->lastInsertId();
            } else {
                $serviceId = $existingService->id;
            }

            // Create a fake order
            $this->db->query("INSERT INTO orders (service_id, buyer_id, seller_id, amount, status, created_at) VALUES (:service_id, :buyer_id, :seller_id, :amount, 'completed', NOW() - INTERVAL " . rand(1, 180) . " DAY)");
            $this->db->bind(':service_id', $serviceId);
            $this->db->bind(':buyer_id', $buyerId);
            $this->db->bind(':seller_id', $adminId);
            $this->db->bind(':amount', $servicePrice);
            $this->db->execute();
            $orderId = $this->db->lastInsertId();

            // Add review with random template
            $reviewText = $reviewTemplates[$i % $reviewCount];
            $rating = 5; // Always 5 stars
            $reviewDate = date('Y-m-d H:i:s', strtotime("-" . rand(1, 180) . " days"));

            $this->db->query("INSERT INTO reviews (order_id, reviewer_id, seller_id, rating, comment, created_at) VALUES (:order_id, :reviewer_id, :seller_id, :rating, :comment, :created_at)");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':reviewer_id', $buyerId);
            $this->db->bind(':seller_id', $adminId);
            $this->db->bind(':rating', $rating);
            $this->db->bind(':comment', $reviewText);
            $this->db->bind(':created_at', $reviewDate);

            if ($this->db->execute()) {
                $createdReviews++;
                echo "Review $createdReviews/180 created successfully\n";
            }
        }

        echo "\nTotal reviews created: $createdReviews\n";
        return true;
    }
}

// Run the review seeder
$seeder = new ReviewSeeder();
$success = $seeder->addReviews();

if ($success) {
    echo "\nSuccessfully added 180 positive reviews for admin!\n";
} else {
    echo "\nFailed to add reviews!\n";
}