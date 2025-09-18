<?php
// Newsletter 数据库表设置脚本
// 将此文件放在网站根目录访问一次来创建数据库表

require_once __DIR__ . '/../app/bootstrap.php';

try {
    // 获取数据库连接
    $db = new Database();

    // 检查表是否已存在
    $db->query("SHOW TABLES LIKE 'newsletter_subscribers'");
    $tableExists = $db->rowCount() > 0;

    if ($tableExists) {
        echo "<h3>✅ Newsletter table already exists!</h3>";
        echo "<p>The newsletter_subscribers table is already set up.</p>";
    } else {
        // 读取并执行SQL文件
        $sqlFile = __DIR__ . '/create_newsletter_table.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);

            // 执行SQL
            $db->query($sql);
            $result = $db->execute();

            if ($result) {
                echo "<h3>✅ Newsletter table created successfully!</h3>";
                echo "<p>The newsletter_subscribers table has been created successfully.</p>";
                echo "<p>You can now access the newsletter management at: <code>/newsletter/admin</code></p>";
            } else {
                echo "<h3>❌ Failed to create table</h3>";
                echo "<p>There was an error creating the database table.</p>";
            }
        } else {
            echo "<h3>❌ SQL file not found</h3>";
            echo "<p>The create_newsletter_table.sql file was not found.</p>";
        }
    }

    echo "<br><a href='/newsletter/admin'>Go to Newsletter Admin</a>";

} catch (Exception $e) {
    echo "<h3>❌ Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>