<?php
// Database connection parameters
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'blog';

try {
    // Connect to MySQL server without specifying a database
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$db_name' created or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE `$db_name`");
    
    // Create blog_posts table
    $sql = "CREATE TABLE IF NOT EXISTS `blog_posts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `excerpt` text,
        `content` text,
        `featured_image` varchar(255),
        `published_date` datetime NOT NULL,
        `updated_date` datetime NOT NULL,
        `tags` text,
        `published` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'blog_posts' created or already exists.<br>";
    
    echo "<p>Setup completed successfully!</p>";
    echo "<p><a href='index.php'>Go to Blog Backend</a></p>";

} catch(PDOException $e) {
    die("ERROR: " . $e->getMessage());
} 