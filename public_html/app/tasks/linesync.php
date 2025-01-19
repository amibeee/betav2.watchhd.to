<?php
namespace Fuel\Tasks;

class Linesync
{
    private const API_BASE_URL = 'http://iptv.watchhd.cc:5050/';
    private const API_TIMEOUT = 10;
    private const VALID_BOUQUETS = [1, 2, 3, 4, 5, 8, 9, 10, 11, 12, 24, 27, 28];
    private const LINE_TYPE_MAIN = 'mainline';
    private const LINE_TYPE_SUB = 'subline';
    
    private static function log($level, $message, $data = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $dataString = empty($data) ? '' : json_encode($data, JSON_PRETTY_PRINT);
        $logMessage = "[{$timestamp}] [{$level}] {$message} {$dataString}";
        
        switch ($level) {
            case 'ERROR':
                \Cli::error($logMessage);
                break;
            case 'SUCCESS':
                \Cli::write($logMessage, 'green');
                break;
            case 'INFO':
                \Cli::write($logMessage, 'blue');
                break;
            case 'WARNING':
                \Cli::write($logMessage, 'yellow');
                break;
            case 'WARN':
                \Cli::write($logMessage, 'red');
                break;
            default:
                \Cli::write($logMessage);
        }
        
        $logFile = APPPATH . 'logs/sync/linesync_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
    }

    public static function run($userId = 0, $startId = null, $endId = null)
    {
        self::log('INFO', 'Starting LineSync process', [
            'userId' => $userId,
            'startId' => $startId,
            'endId' => $endId,
            'timestamp' => time()
        ]);

        try {
            // Process main lines from Model_User
            $userQuery = self::buildUserQuery($userId, $startId, $endId);
            $users = $userQuery->order_by('id', 'ASC')->get();
            
            self::log('WARN', 'Found users to process', [
                'count' => count($users)
            ]);
            
            foreach($users as $user) {
                self::processMainUser($user);
                usleep(200000); // Rate limiting
            }

            // Process sublines from Model_User_Line
            $lineQuery = self::buildLineQuery($userId);
            $lines = $lineQuery->order_by('id', 'DESC')->get();

            self::log('WARN', 'Found sublines to process', [
                'count' => count($lines)
            ]);

            foreach($lines as $line) {
                self::processSubline($line);
                usleep(200000); // Rate limiting
            }
            
            self::log('SUCCESS', 'LineSync process completed successfully');
            
        } catch (\Exception $e) {
            self::log('ERROR', 'LineSync process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private static function buildUserQuery($userId, $startId, $endId)
    {
        $query = \Model_User::query()->where('premium_until', '>', time());
        
        if($userId) {
            $query->where('id', $userId);
        }
        
        if ($startId !== null && $endId !== null) {
            $query->where('id', '>=', (int) $startId)
                  ->where('id', '<=', (int) $endId);
        }
        
        return $query;
    }

    private static function buildLineQuery($userId)
    {
        $query = \Model_User_Line::query()
            ->where('premium_until', '>', time());
        
        if($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query;
    }

    private static function processMainUser($user)
    {
        self::log('INFO', 'Processing main user', [
            'userId' => $user->id,
            'username' => $user->username
        ]);

        try {
            $postData = [
                'username' => $user->username,
                'password' => $user->line_password,
                'exp_date' => $user->premium_until,
                'bouquet' => json_encode(self::getMainLineBouquet($user)),
                'max_connections' => self::getMainLineConnections($user),
                'line_type' => self::LINE_TYPE_MAIN
            ];

            self::updateUserApi($user, $postData);

        } catch (\Exception $e) {
            self::log('ERROR', 'Failed to process main user', [
                'userId' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private static function processSubline($line)
    {
        self::log('INFO', 'Processing subline', [
            'userId' => $line->user_id,
            'username' => $line->username
        ]);

        try {
            $user = \Model_User::find($line->user_id);
            if (!$user) {
                throw new \Exception("User not found for subline");
            }

            $postData = [
                'username' => $line->username,
                'password' => $line->password,
                'exp_date' => $line->premium_until,
                'bouquet' => json_encode(self::getSubLineBouquet($line)),
                'max_connections' => self::getSubLineConnections($line),	
                'line_type' => self::LINE_TYPE_SUB,
                'parent_line_id' => $line->id
            ];

            self::updateUserApi($user, $postData);

        } catch (\Exception $e) {
            self::log('ERROR', 'Failed to process subline', [
                'lineId' => $line->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private static function getMainLineConnections($user)
    {
        // Check if user has any active payments with multiline
        $payment = \Model_User_Payment::query()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->where('data', 'LIKE', '%"username":"' . $user->username . '"%')
            ->order_by('id', 'desc')
            ->get_one();

        if ($payment) {
            $paymentData = json_decode($payment['data'], true);
            if ($paymentData && 
                isset($paymentData['buy']['multiline']) && 
                $paymentData['buy']['multiline'] === '1') {
                return 2;
            }
        }

        return 1;
    }
    private static function getMainLineBouquet($user)
    {
        $bouquets = [];
        $payment = \Model_User_Payment::query()
        
        ->where('user_id', $user->id)
        ->where('status', 'paid')
        ->where('data', 'LIKE', '%"username":"' . $user->username . '"%')
        ->order_by('id', 'desc')
        ->get_one();

    if ($payment) {

        $paymentData = json_decode($payment['data'], true);
        if (isset($paymentData['packages']) && is_array($paymentData['packages'])) {
            foreach ($paymentData['packages'] as $bouquetId) {
                if (in_array($bouquetId, self::VALID_BOUQUETS)) {
                    $bouquets[] = $bouquetId;
                }
            }
        }
        
        return array_values(array_unique($bouquets));
     }
}
private static function getSubLineBouquet($line)
{
    $bouquets = [];
    $payment = \Model_User_Payment::query()
    
    ->where('user_id', $line->user_id)
    ->where('status', 'paid')
    ->where('data', 'LIKE', '%"username":"' . $line->username . '"%')
    ->order_by('id', 'desc')
    ->get_one();

if ($payment) {

    $paymentData = json_decode($payment['data'], true);
    if (isset($paymentData['packages']) && is_array($paymentData['packages'])) {
        foreach ($paymentData['packages'] as $bouquetId) {
            if (in_array($bouquetId, self::VALID_BOUQUETS)) {
                $bouquets[] = $bouquetId;
            }
        }
    }
    
    return array_values(array_unique($bouquets));
 }
}
    private static function getSublineConnections($line)
    {
        // Check if user has any active payments with multiline
        $payment = \Model_User_Payment::query()
            ->where('user_id', $line->user_id)
            ->where('status', 'paid')
            ->where('data', 'LIKE', '%"username":"' . $line->username . '"%')
            ->order_by('id', 'desc')
            ->get_one();

        if ($payment) {
            $paymentData = json_decode($payment['data'], true);
            if ($paymentData && 
                isset($paymentData['buy']['multiline']) && 
                $paymentData['buy']['multiline'] === '1') {
                return 2;
            }
        }

        return 1;
    }
    private static function updateUserApi($user, $postData)
    {
        try {
            self::log('INFO', 'Sending API request', [
                'userId' => $user->id,
                'endpoint' => self::API_BASE_URL . "api/api.php?action=update_user",
                'postData' => $postData
            ]);

            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($postData),
                    'timeout' => self::API_TIMEOUT
                ]
            ];
            
            $context = stream_context_create($opts);
            $response = file_get_contents(self::API_BASE_URL . "api/api.php?action=update_user", false, $context);
            
            if ($response === false) {
                throw new \Exception("API request failed");
            }
            
            $apiResult = json_decode($response);
            
            if (isset($apiResult->success) && $apiResult->success) {
                self::log('SUCCESS', 'Line updated successfully', [
                    'userId' => $user->id,
                    'username' => $postData['username'],
                    'lineType' => $postData['line_type']
                ]);
            } else {
                self::log('ERROR', 'API returned error response', [
                    'userId' => $user->id,
                    'username' => $postData['username'],
                    'response' => $response
                ]);
            }
            
        } catch (\Exception $e) {
            self::log('ERROR', 'API request failed', [
                'userId' => $user->id,
                'username' => $postData['username'],
                'error' => $e->getMessage()
            ]);
        }
    }
}