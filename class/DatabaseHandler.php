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


    protected function tableExists($table): bool
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->bindParam(':table', $table);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }


    protected function columnExists($table, $column): bool
    {
        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
        $stmt->bindParam(':column', $column);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function selectAsync($dbTable, array $conditions = [], $fetchAll = false)
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "`$column` = :$column";
            $params[":$column"] = $value;
        }

        $whereSql = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";
        $query = /** @lang text */
            "SELECT * FROM `$dbTable` $whereSql";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAsync($dbTable, array $data)
    {
        $columns = implode("`, `", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $query = /** @lang text */
            "INSERT INTO `$dbTable` (`$columns`) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($data) ? $this->pdo->lastInsertId() : false;
    }

    /**
     * @throws Exception
     */
    public function updateAsync($dbTable, array $data, array $conditions): bool
    {
        $setClauses = [];
        $whereClauses = [];
        $params = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "`$column` = :set_$column";
            $params[":set_$column"] = $value;
        }

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "`$column` = :where_$column";
            $params[":where_$column"] = $value;
        }

        if (empty($whereClauses)) {
            throw new Exception("Update must have a WHERE condition to prevent mass updates.");
        }

        $setSql = implode(", ", $setClauses);
        $whereSql = implode(" AND ", $whereClauses);
        $query = /** @lang text */
            "UPDATE `$dbTable` SET $setSql WHERE $whereSql";

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    public function deleteAsync($dbTable, array $conditions): bool
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "`$column` = :$column";
            $params[":$column"] = $value;
        }

        if (empty($whereClauses)) {
            throw new Exception("Delete must have a WHERE condition to prevent mass deletions.");
        }

        $whereSql = implode(" AND ", $whereClauses);
        $query = /** @lang text */
            "DELETE FROM `$dbTable` WHERE $whereSql";

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Validate table and column names dynamically.
     * @throws Exception
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

    /**
     * @throws Exception
     */
    public function existingData($dbTable, $dbColumn, $value)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $stmt = $this->pdo->prepare(/** @lang text */ "SELECT COUNT(*) FROM `$dbTable` WHERE `$dbColumn` = :val");
        $stmt->bindParam(":val", $value);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @throws Exception
     */
    public function countData($dbTable)
    {
        $this->validateTableAndColumn($dbTable);

        $stmt = $this->pdo->prepare(/** @lang text */ "SELECT COUNT(*) FROM `$dbTable`");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @throws Exception
     */
    public function countDataWithConditions($dbTable, array $conditions = [])
    {
        $this->validateTableAndColumn($dbTable);

        // Base query
        $query = /** @lang text */
            "SELECT COUNT(*) as total FROM `$dbTable`";

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $condition) {
                $this->validateTableAndColumn($dbTable, $condition['column']);

                $whereClauses[] = "`{$condition['column']}` {$condition['operator']} :{$condition['column']}";
            }
            $query .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $stmt = $this->pdo->prepare($query);

        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                $stmt->bindValue(
                    ":{$condition['column']}",
                    $condition['value'],
                    is_int($condition['value']) ? \PDO::PARAM_INT : \PDO::PARAM_STR
                );
            }
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @throws Exception
     */
    public function countDataMultiple($dbTable, $conditions = [])
    {
        $this->validateTableAndColumn($dbTable);

        $query = /** @lang text */
            "SELECT COUNT(*) FROM `$dbTable`";

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
        return $stmt->fetchColumn();
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

    /**
     * @throws Exception
     */
    public function selectAllData($dbTable, $condition = '')
    {
        $this->validateTableAndColumn($dbTable);

        $selectQuery = /** @lang text */
            "SELECT * FROM `$dbTable`";
        if (!empty($condition)) {
            $selectQuery .= " $condition";
        }

        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->execute();
        $selectTable = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        return $selectTable ?: false;
    }

    /**
     * @throws Exception
     */
    public function selectData($dbTable, $dbColumn, $condition)
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $selectQuery = /** @lang text */
            "SELECT * FROM `$dbTable` WHERE `$dbColumn` = :condition";
        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->bindParam(':condition', $condition);
        $selectStmt->execute();
        $selectTable = $selectStmt->fetch(PDO::FETCH_ASSOC);

        return $selectTable ?: false;
    }

    /**
     * @throws Exception
     */
    public function insertData($tbName, $dbColumn, $value)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);

        $insertQuery = /** @lang text */
            "INSERT INTO `$tbName` (`$dbColumn`) VALUES (:val)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function insertTheData($tbName, $dbColumn, $value)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);

        $insertQuery = /** @lang text */
            "INSERT INTO `$tbName` (`$dbColumn`) VALUES (:val)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function insertTwoData($tbName, $dbColumn, $dbColumn2, $value, $value2)
    {
        $this->validateTableAndColumn($tbName, $dbColumn);
        $this->validateTableAndColumn($tbName, $dbColumn2);

        $insertQuery = /** @lang text */
            "INSERT INTO `$tbName` (`$dbColumn`, `$dbColumn2`) VALUES (:val, :val2)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':val', $value);
        $insertStmt->bindParam(':val2', $value2);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function updateData($dbTable, $dbColumn, $newRecord, $condition, $id): bool
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);
        $this->validateTableAndColumn($dbTable, $condition);

        $updateQuery = /** @lang text */
            "UPDATE `$dbTable` SET `$dbColumn` = :newRecord WHERE `$condition` = :id";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':newRecord', $newRecord);
        $updateStmt->bindParam(':id', $id);
        return $updateStmt->execute();
    }

    /**
     * @throws Exception
     */
    public function update($dbTable, $dbColumn, $newRecord): bool
    {
        $this->validateTableAndColumn($dbTable, $dbColumn);

        $updateQuery = /** @lang text */
            "UPDATE `$dbTable` SET `$dbColumn` = :newRecord";
        $updateStmt = $this->pdo->prepare($updateQuery);
        $updateStmt->bindParam(':newRecord', $newRecord);
        return $updateStmt->execute();
    }

    /**
     * @throws Exception
     */
    public function insertUser($firstName, $lastName, $email, $password, $country, $type, $last_login, $joined, $email_token, $remember_token)
    {
        $this->validateTableAndColumn('users');

        $insertQuery = /** @lang text */
            "INSERT INTO `users` (firstName, lastName, email, country, password, type, online, last_login, joined, email_token, remember_token) VALUES (:firstName, :lastName, :email, :country, :password, :type, '1', :last_login, :joined, :email_token, :remember_token)";
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

    /**
     * @throws Exception
     */
    public function getAllUser()
    {
        $this->validateTableAndColumn('users');

        $selectQuery = /** @lang text */
            "SELECT * FROM `users`";
        $selectStmt = $this->pdo->prepare($selectQuery);
        $selectStmt->execute();
        $getId = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        return $getId ?: false;
    }

    /**
     * @throws Exception
     */
    public function insertCategoryTags($tbName, $title, $desc, $url)
    {
        $this->validateTableAndColumn($tbName);

        $insertQuery = /** @lang text */
            "INSERT INTO `$tbName` (name, description, url) VALUES (:name, :description, :url)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':name', $title);
        $insertStmt->bindParam(':description', $desc);
        $insertStmt->bindParam(':url', $url);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function insertStaff($email, $password)
    {
        $this->validateTableAndColumn('staff');

        $insertQuery = /** @lang text */
            "INSERT INTO `staff` (email, password) VALUES (:email, :password)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':password', $password);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function insertContact($firstName, $lastName, $email, $phone_code, $phone)
    {
        $this->validateTableAndColumn('users');

        $insertQuery = /** @lang text */
            "INSERT INTO `users` (first_name, last_name, email, phone_code, phone) VALUES (:first_name, :last_name, :email, :phone_code, :phone)";
        $insertStmt = $this->pdo->prepare($insertQuery);
        $insertStmt->bindParam(':first_name', $firstName);
        $insertStmt->bindParam(':last_name', $lastName);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':phone_code', $phone_code);
        $insertStmt->bindParam(':phone', $phone);
        $insertStmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    public function deleteData($dbName, $dbColumn, $condition): bool
    {
        $this->validateTableAndColumn($dbName, $dbColumn);

        $deleteQuery = /** @lang text */
            "DELETE FROM `$dbName` WHERE `$dbColumn` = :condition";
        $deleteStmt = $this->pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':condition', $condition);
        return $deleteStmt->execute();
    }

    /**
     * @throws Exception
     */
    public function onlineStatusUpdate($dbName, $condition, $userId): bool
    {
        $this->validateTableAndColumn($dbName, $condition);

        $query = /** @lang text */
            "UPDATE `$dbName` SET last_seen = NOW() WHERE `$condition` = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}