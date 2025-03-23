<?php

namespace config;

// Initialize Database Connection
require_once __DIR__ . '/../class/DatabaseConnect.php';
require_once __DIR__ . '/../class/DatabaseHandler.php';
require_once __DIR__ . '/../class/Request.php';
require_once __DIR__ . '/../class/AuthMiddleware.php';
require_once __DIR__ . '/../class/ApiHandler.php';

use ApiHandler;
use AuthMiddleware;
use DatabaseConnection;
use DatabaseHandler;
use Exception;
use PDO;
use Request;

class Config
{
    private static $instance = null;
    private $pdo;
    private $dbHandler;
    private $requestModel;
    private static $settings = [];
    private static $authUser = null;

    private function __construct()
    {
        // Set Time Zone
        date_default_timezone_set('Africa/Accra');

        $this->pdo = DatabaseConnection::connect();
        $this->dbHandler = new DatabaseHandler($this->pdo);
        $this->requestModel = new Request();

        $this->loadSettings();
        $this->handleCORS();
        $this->loadUtilities();
    }

    private function loadSettings()
    {
        if (!empty(self::$settings)) {
            return; // Prevent reloading if already loaded
        }

        $dbHandler = $this->getDbHandler(); // Ensure dbHandler is initialized

        // Fetch Email Configuration
        self::$settings['smtp'] = $dbHandler->selectData('app_system', 'system', 'smtp')['value'] ?? null;
        self::$settings['smtp_username'] = $dbHandler->selectData('app_system', 'system', 'smtp_username')['value'] ?? null;
        self::$settings['smtp_password'] = $dbHandler->selectData('app_system', 'system', 'smtp_password')['value'] ?? null;

        // WooCommerce API Keys
        self::$settings['wc_store'] = $dbHandler->selectData('app_system', 'system', 'wc_store')['value'] ?? null;
        self::$settings['wc_consumer_key'] = $dbHandler->selectData('app_system', 'system', 'wc_consumer_key')['value'] ?? null;
        self::$settings['wc_secrete_key'] = $dbHandler->selectData('app_system', 'system', 'wc_secrete_key')['value'] ?? null;

        error_log("Settings loaded successfully");
    }

    private function loadAuthUser()
    {
        if (self::$authUser !== null) {
            return; // Prevent reloading if already set
        }

        self::$authUser = AuthMiddleware::validateToken();
    }

    private function handleCORS()
    {
        $allowed_origins = [
            'https://portal.damnmodz.com',
            'https://damnmodz.com',
            'http://localhost:5173'
        ];

        if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header('Access-Control-Max-Age: 86400'); // Cache for 1 day
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    private function loadUtilities()
    {
        require_once __DIR__ . '/../class/ApiHandler.php';
        require_once __DIR__ . '/../class/csrf_token.php';
        require_once __DIR__ . '/../class/Utils.php';
        require_once __DIR__ . '/../class/ResponseSigner.php';

        require_once __DIR__ . '/../functions/function.php';
        require_once __DIR__ . '/../functions/timeago.php';
        require_once __DIR__ . '/../functions/mailer.php';
        require_once __DIR__ . '/../functions/logs.php';
        require_once __DIR__ . '/../functions/notification.php';
        require_once __DIR__ . '/../functions/session.php';
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getDbHandler()
    {
        return $this->dbHandler;
    }

    public function getRequestModel()
    {
        return $this->requestModel;
    }

    public function getSetting($key)
    {
        return self::$settings[$key] ?? null;
    }

    public function getAuthUser()
    {
        $user = AuthMiddleware::validateToken();

        self::$authUser = $user;

        return self::$authUser;
    }
}

