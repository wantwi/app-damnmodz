<?php


class DatabaseHandler
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        if ($this->pdo === null) {
            $this->pdo = $pdo;
        }
    }

    /**
     * Check if a table exists in the database.
     */
    protected function tableExists($table)
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->bindParam(':table', $table);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a column exists in a table.
     */
    protected function columnExists($table, $column)
    {
        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
        $stmt->bindParam(':column', $column);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Validate table and column names dynamically.
     */
    protected function validateTableAndColumn($table, $column = null)
    {
        if (!$this->tableExists($table)) {
            throw new Exception("Table '$table' does not exist.");
        }

        if ($column !== null && !$this->columnExists($table, $column)) {
            throw new Exception("Column '$column' does not exist in table '$table'.");
        }
    }

    public function existingData($dbTable, $dbColumn, $value)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `$dbTable` WHERE `$dbColumn` = :val");
        $stmt->bindParam(":val", $value);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function countData($dbTable)
    {
        $this->validateTableAndColumn($dbTable);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `$dbTable`");
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count;
    }

    public function countDataMultiple($dbTable, $conditions = [])
    {
        $this->validateTableAndColumn($dbTable);

        $query = "SELECT COUNT(*) FROM `$dbTable`";

        if (!empty($conditions)) {
            $whereClause = [];
            $params = [];

            foreach ($conditions as $column => $value) {
                $this->validateTableAndColumn($dbTable, $column);

                $whereClause[] = "`$column` = :$column";
                $params[":$column"] = $value;
            }

            $query .= " WHERE " . implode(" AND ", $whereClause);
        }

        $stmt = $this->pdo->prepare($query);

        if (!empty($conditions)) {
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
        }

        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count;
    }

    public function executeCustomQuery($query, $params = [])
    {
        try {
            // Prepare the query
            $stmt = $this->pdo->prepare($query);

            // Bind parameters if provided
            if (!empty($params)) {
                foreach ($params as $param => $value) {
                    $stmt->bindValue($param, $value);
                }
            }

            // Execute the query
            $stmt->execute();

            // Fetch and return the results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error or handle it as needed
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function selectAllData($dbTable, $condition = '')
    {
        $this->validateTableAndColumn($dbTable);

        $selectQuery = "SELECT * FROM `$dbTable`";
        if (!empty($condition)) {
            $selectQuery .= " $condition";
        }

        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->execute();
        $selectTable = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        return $selectTable ?: false;
    }

    public function selectData($dbTable, $dbColumn, $condition)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $selectQuery = "SELECT * FROM `$dbTable` WHERE `$dbColumn` = :condition";
        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->bindParam(':condition', $condition);
        $selectStmt->execute();
        $selectTable = $selectStmt->fetch(PDO::FETCH_ASSOC);

        return $selectTable ?: false;
    }

    public function insertData($tbName, $dbColumn, $value)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);

        $insertQuery = "INSERT INTO `$tbName` (`$dbColumn`) VALUES (:val)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function insertTheData($tbName, $dbColumn, $value)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);

        $insertQuery = "INSERT INTO `$tbName` (`$dbColumn`) VALUES (:val)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function insertTwoData($tbName, $dbColumn, $dbColumn2, $value, $value2)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);
        $this->validateTableAndColumn($tbName, $dbColumn2);

        $insertQuery = "INSERT INTO `$tbName` (`$dbColumn`, `$dbColumn2`) VALUES (:val, :val2)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->bindParam(':val2', $value2);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function updateData($dbTable, $dbColumn, $newRecord, $condition, $id)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);
        $this->validateTableAndColumn($dbTable, $condition);

        $updateQuery = "UPDATE `$dbTable` SET `$dbColumn` = :newRecord WHERE `$condition` = :id";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':newRecord', $newRecord);
        $updateStmt->bindParam(':id', $id);
        $updated = $updateStmt->execute();

        return $updated;
    }

    public function update($dbTable, $dbColumn, $newRecord)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $updateQuery = "UPDATE `$dbTable` SET `$dbColumn` = :newRecord";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':newRecord', $newRecord);
        $updated = $updateStmt->execute();

        return $updated;
    }

    public function insertUser($firstName, $lastName, $email, $password, $country, $type, $last_login, $joined, $email_token, $remember_token)
    {
        $this->validateTableAndColumn('users');

        $insertQuery = "INSERT INTO `users` (firstName, lastName, email, country, password, type, online, last_login, joined, email_token, remember_token) VALUES (:firstName, :lastName, :email, :country, :password, :type, '1', :last_login, :joined, :email_token, :remember_token)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':firstName', $firstName);
        $insertStmt->bindParam(':lastName', $lastName);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':country', $country);
        $insertStmt->bindParam(':password', $password);
        $insertStmt->bindParam(':type', $type);
        $insertStmt->bindParam(':last_login', $last_login);
        $insertStmt->bindParam(':joined', $joined);
        $insertStmt->bindParam(':email_token', $email_token);
        $insertStmt->bindParam(':remember_token', $remember_token);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function getAllUser()
    {
        $this->validateTableAndColumn('users');

        $selectQuery = "SELECT * FROM `users`";
        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->execute();
        $getId = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        return $getId ?: false;
    }

    public function insertCategoryTags($tbName, $title, $desc, $url)
    {
        $this->validateTableAndColumn($tbName);

        $insertQuery = "INSERT INTO `$tbName` (name, description, url) VALUES (:name, :description, :url)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':name', $title);
        $insertStmt->bindParam(':description', $desc);
        $insertStmt->bindParam(':url', $url);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function insertStaff($email, $password)
    {
        $this->validateTableAndColumn('staff');

        $insertQuery = "INSERT INTO `staff` (email, password) VALUES (:email, :password)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':password', $password);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function insertContact($firstName, $lastName, $email, $phone_code, $phone)
    {
        $this->validateTableAndColumn('users');

        $insertQuery = "INSERT INTO `users` (first_name, last_name, email, phone_code, phone) VALUES (:first_name, :last_name, :email, :phone_code, :phone)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':first_name', $firstName);
        $insertStmt->bindParam(':last_name', $lastName);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':phone_code', $phone_code);
        $insertStmt->bindParam(':phone', $phone);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function insertProduct($name, $category_id, $subcategory, $tags, $description, $basicZip, $fullZip, $wordpressZip, $shopifyZip, $live_url, $youtube_link, $bootstrap_v, $webserver_com, $database_server, $php_v, $basicPrice, $fullPrice, $wordpress_price, $shopify_price, $wix, $discount, $product_url, $product_img)
    {
        $this->validateTableAndColumn('products');

        $insertQuery = "INSERT INTO `products` (name, category_id, subcategory, tags, description, basicZip, fullZip, wordpressZip, shopifyZip, live_url, youtube_link, bootstrap_v, webserver_com, database_server, php_v, basicPrice, fullPrice, wordpress_price, shopify_price, wix, discount, product_url, product_img) VALUES (:name, :category_id, :subcategory, :tags, :description, :basicZip, :fullZip, :wordpressZip, :shopifyZip, :live_url, :youtube_link, :bootstrap_v, :webserver_com, :database_server, :php_v, :basicPrice, :fullPrice, :wordpress_price, :shopify_price, :wix, :discount, :product_url, :product_img)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':category_id', $category_id);
        $insertStmt->bindParam(':subcategory', $subcategory);
        $insertStmt->bindParam(':tags', $tags);
        $insertStmt->bindParam(':description', $description);
        $insertStmt->bindParam(':basicZip', $basicZip);
        $insertStmt->bindParam(':fullZip', $fullZip);
        $insertStmt->bindParam(':wordpressZip', $wordpressZip);
        $insertStmt->bindParam(':shopifyZip', $shopifyZip);
        $insertStmt->bindParam(':live_url', $live_url);
        $insertStmt->bindParam(':youtube_link', $youtube_link);
        $insertStmt->bindParam(':bootstrap_v', $bootstrap_v);
        $insertStmt->bindParam(':webserver_com', $webserver_com);
        $insertStmt->bindParam(':database_server', $database_server);
        $insertStmt->bindParam(':php_v', $php_v);
        $insertStmt->bindParam(':basicPrice', $basicPrice);
        $insertStmt->bindParam(':fullPrice', $fullPrice);
        $insertStmt->bindParam(':wordpress_price', $wordpress_price);
        $insertStmt->bindParam(':shopify_price', $shopify_price);
        $insertStmt->bindParam(':wix', $wix);
        $insertStmt->bindParam(':discount', $discount);
        $insertStmt->bindParam(':product_url', $product_url);
        $insertStmt->bindParam(':product_img', $product_img);
        $insertStmt->execute();

        return $insertStmt ? $this->pdo->lastInsertId() : false;
    }

    public function deleteData($dbName, $dbColumn, $condition)
    {
        $this->validateTableAndColumn($dbName, $dbColumn);

        $deleteQuery = "DELETE FROM `$dbName` WHERE `$dbColumn` = :condition";
        $deleteStmt = $this->pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':condition', $condition);
        $deleted = $deleteStmt->execute();

        return $deleted;
    }

    public function onlineStatusUpdate($dbName, $condition, $userId)
    {
        $this->validateTableAndColumn($dbName, $condition);

        $query = "UPDATE `$dbName` SET last_seen = NOW() WHERE `$condition` = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}


//class DatabaseHandler
//{
//    protected $pdo;
//
//    public function __construct(PDO $pdo)
//    {
//        if ($this->pdo === null) {
//            $this->pdo = $pdo;
//        }
//
//    }
//    public function existingData($dbTable, $dbColumn, $value)
//    {
//       // $pdo = DatabaseConnection::connect();
//        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $dbTable WHERE $dbColumn = :val");
//        $stmt->bindParam(":val", $value);
//        $stmt->execute();
//        $count = $stmt->fetch();
//
//        return $count;
//    }
//
//    public function countData($dbTable, $dbColumn)
//    {
//       // $pdo = DatabaseConnection::connect();
//        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `$dbTable`");
//        // Execute the prepared statement with the current email
//        $stmt->execute();
//        $count = $stmt->fetch();
//
//        return $count;
//    }
//
//
//    public function selectAllData($dbTable, $condition)
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $selectQuery = "SELECT * from $dbTable $condition";
//        $selectStmt = $this->pdo->prepare($selectQuery);
//        $selectStmt->execute();
//        $selectTable = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
//
//        if ($selectTable) {
//            # code...
//            return $selectTable;
//        } else {
//            return false;
//        }
//    }
//
//    public function selectData($dbTable, $dbColumn, $condition)
//    {
//        $selectQuery = "SELECT * FROM $dbTable WHERE $dbTable.$dbColumn = :condition";
//
//        $selectStmt = $this->pdo->prepare($selectQuery);
//        $selectStmt->bindParam(':condition', $condition);
//        $selectStmt->execute();
//        $selectTable = $selectStmt->fetch(PDO::FETCH_ASSOC);
//        return $selectTable ?: false;
//    }
//
//    public function insertData($tbName, $dbColumn, $value)
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO $tbName ($dbColumn) VALUES (:val)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':val', $value);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    // Updted verison
//
//    public function insertTheData($tbName, $dbColumn, $value)
//{
//       // $pdo = DatabaseConnection::connect();
//
//        // Prepare and execute the insert query
//        $insertQuery = "INSERT INTO $tbName ($dbColumn) VALUES (:val)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':val', $value);
//        $insertStmt->execute();
//
//        // Check if the insert was successful
//        if ($insertStmt) {
//            // Return the last inserted ID
//            return $this->pdo->lastInsertId();
//        } else {
//            return false;
//        }
//}
//
//    public function insertTwoData($tbName, $dbColumn, $dbColumn2, $value, $value2)
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO $tbName ($dbColumn, $dbColumn2) VALUES (:val, :val2)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':val', $value);
//        $insertStmt->bindParam(':val2', $value2);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function updateData($dbTable, $dbColumn, $newRecord, $condition, $id)
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $updateQuery = "UPDATE $dbTable SET $dbColumn = :newRecord WHERE $condition = :id";
//        $updateStmt = $this->pdo->prepare($updateQuery);
//        $updateStmt->bindParam(':newRecord', $newRecord);
//        $updateStmt->bindParam(':id', $id);
//        $updated = $updateStmt->execute();
//
//        if ($updated) {
//            # code...
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//public function update($dbTable, $dbColumn, $newRecord)
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $updateQuery = "UPDATE $dbTable SET $dbColumn = :newRecord ";
//        $updateStmt = $this->pdo->prepare($updateQuery);
//        $updateStmt->bindParam(':newRecord', $newRecord);
//        $updated = $updateStmt->execute();
//
//        if ($updated) {
//            # code...
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function insertUser($firstName, $lastName, $email, $password, $country, $type, $last_login, $joined, $email_token, $remember_token)
//    {
//
//       // $pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO users (firstName, lastName, email, country, password, type, online, last_login, joined, email_token, remember_token) VALUES (:firstName, :lastName,:email, :country,:password, :type, '1', :last_login, :joined, :email_token, :remember_token)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':firstName', $firstName);
//        $insertStmt->bindParam(':lastName', $lastName);
//        $insertStmt->bindParam(':email', $email);
//        $insertStmt->bindParam(':country', $country);
//        $insertStmt->bindParam(':password', $password);
//        $insertStmt->bindParam(':type', $type);
//        $insertStmt->bindParam(':last_login', $last_login);
//        $insertStmt->bindParam(':joined', $joined);
//        $insertStmt->bindParam(':email_token', $email_token);
//        $insertStmt->bindParam(':remember_token', $remember_token);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function getAllUser()
//
//    {
//       // $pdo = DatabaseConnection::connect();
//
//        $selectQuery = "SELECT * from users";
//        $selectStmt = $this->pdo->prepare($selectQuery);
//        $selectStmt->execute();
//        $getId = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
//
//        if ($getId) {
//            return $getId;
//        }
//    }
//
//    public function insertCategoryTags($tbName, $title, $desc, $url)
//    {
//
//        //$pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO $tbName (name, description,url) VALUES (:name, :description, :url)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':name', $title);
//        $insertStmt->bindParam(':description', $desc);
//        $insertStmt->bindParam(':url', $url);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function insertStaff($email, $password)
//    {
//
//        //$pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO staff (email, password) VALUES (:email, :password)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':email', $email);
//        $insertStmt->bindParam(':password', $password);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function insertContact($firstName, $lastName, $email, $phone_code, $phone)
//    {
//
//        //$pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO users (first_name, last_name, email, phone_code, phone) VALUES (:first_name, :last_name, :email, :phone_code, :phone)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':first_name', $firstName);
//        $insertStmt->bindParam(':last_name', $lastName);
//        $insertStmt->bindParam(':email', $email);
//        $insertStmt->bindParam(':phone_code', $phone_code);
//        $insertStmt->bindParam(':phone', $phone);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function insertProduct($name, $category_id, $subcategory, $tags, $description, $basicZip, $fullZip, $wordpressZip, $shopifyZip, $live_url, $youtube_link, $bootstrap_v, $webserver_com, $database_server, $php_v, $basicPrice, $fullPrice, $wordpress_price, $shopify_price, $wix, $discount, $product_url, $product_img)
//    {
//
//        //$pdo = DatabaseConnection::connect();
//
//        $insertQuery = "INSERT INTO products (name,category_id,subcategory,tags,description,basicZip,fullZip,wordpressZip,shopifyZip,live_url,youtube_link,bootstrap_v,webserver_com,database_server,php_v,basicPrice,fullPrice,wordpress_price,shopify_price,wix,discount,product_url,product_img) VALUES (:name, :category_id, :subcategory, :tags,	:description, :basicZip, :fullZip, :wordpressZip, :shopifyZip, :live_url, :youtube_link, :bootstrap_v, :webserver_com,	:database_server, :php_v, :basicPrice, :fullPrice, :wordpress_price, :shopify_price, :wix, :discount, :product_url, :product_img)";
//        $insertStmt = $this->pdo->prepare($insertQuery);
//        $insertStmt->bindParam(':name', $name);
//        $insertStmt->bindParam(':category_id', $category_id);
//        $insertStmt->bindParam(':subcategory', $subcategory);
//        $insertStmt->bindParam(':tags', $tags);
//        $insertStmt->bindParam(':description', $description);
//        $insertStmt->bindParam(':basicZip', $basicZip);
//        $insertStmt->bindParam(':fullZip', $fullZip);
//        $insertStmt->bindParam(':wordpressZip', $wordpressZip);
//        $insertStmt->bindParam(':shopifyZip', $shopifyZip);
//        $insertStmt->bindParam(':live_url', $live_url);
//        $insertStmt->bindParam(':youtube_link', $youtube_link);
//        $insertStmt->bindParam(':bootstrap_v', $bootstrap_v);
//        $insertStmt->bindParam(':webserver_com', $webserver_com);
//        $insertStmt->bindParam(':database_server', $database_server);
//        $insertStmt->bindParam(':php_v', $php_v);
//        $insertStmt->bindParam(':basicPrice', $basicPrice);
//        $insertStmt->bindParam(':fullPrice', $fullPrice);
//        $insertStmt->bindParam(':wordpress_price', $wordpress_price);
//        $insertStmt->bindParam(':shopify_price', $shopify_price);
//        $insertStmt->bindParam(':wix', $wix);
//        $insertStmt->bindParam(':discount', $discount);
//        $insertStmt->bindParam(':product_url', $product_url);
//        $insertStmt->bindParam(':product_img', $product_img);
//        $insertStmt->execute();
//
//        if ($insertStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function deleteData($dbName, $dbColumn, $condition)
//    {
//
//        //$pdo = DatabaseConnection::connect();
//
//        $deleteQuery = "DELETE FROM $dbName WHERE $dbColumn = $condition";
//        $deleteStmt = $this->pdo->prepare($deleteQuery);
//        $deleteStmt->execute();
//
//        if ($deleteStmt) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function onlineStatusUpdate($dbName, $condition, $userId)
//    {
//       //$pdo = DatabaseConnection::connect();
//
//        $query = "UPDATE $dbName SET last_seen = NOW() WHERE $condition = :user_id";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->bindParam(':user_id', $userId);
//
//        if($stmt->execute()){
//            return true;
//        }else{
//            return false;
//        }
//    }
//
//}
