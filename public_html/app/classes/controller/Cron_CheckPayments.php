<?php
// Database connection settings
$host = 'localhost';
$dbname = 'sucuk_site';
$username = 'sucuk_site';
$password = '6LVHAtKbaM6v1HGV';

// API Key and configuration
define('API_KEY', 'ik3r4BL9WcXwVB6qa61mNaf3Tc5YUY');
define('STATUS_URL', 'https://trocador.app/de/anonpay/status/');
define('MAX_RETRIES', 3);
define('RETRY_DELAY', 0); // seconds

// Status mappings
$status_mappings = [
    'anonpaynew' => 'pending',
    'waiting' => 'pending',
    'confirming' => 'confirming',
    'sending' => 'processing',
    'paid partially' => 'partial',
    'finished' => 'paid',
    'failed' => 'failed',
    'expired' => 'expired',
    'halted' => 'failed',
    'refunded' => 'refunded'
];

// Create a new MySQLi connection
$mysqli = createDatabaseConnection($host, $username, $password, $dbname);

// Fetch all pending payments with idtrc not null
$query = "SELECT id, idtrc, status_trc FROM user_payments 
          WHERE status NOT IN ('paid', 'declined', 'timeout') 
          AND idtrc IS NOT NULL";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $idtrc = $row['idtrc'];
        $payment_id = $row['id'];

        $status = fetchPaymentStatus($idtrc);
        if ($status !== null) {
            updatePaymentStatus($mysqli, $payment_id, $idtrc, $status, $status_mappings);
        }
    }
} else {
    error_log("Error fetching payments: " . $mysqli->error);
}

// Close the database connection
$mysqli->close();

/**
 * Create a new MySQLi database connection.
 */
function createDatabaseConnection($host, $username, $password, $dbname)
{
    $mysqli = new mysqli($host, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        error_log("Database connection failed: " . $mysqli->connect_error);
        die("Connection failed: " . $mysqli->connect_error);
    }
    return $mysqli;
}

/**
 * Fetch the payment status from the API.
 */
function fetchPaymentStatus($idtrc)
{
    $headers = [
        "API-Key: " . API_KEY,
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    $options = [
        "http" => [
            "method" => "GET",
            "header" => implode("\r\n", $headers)
        ]
    ];

    $context = stream_context_create($options);

    for ($retry = 0; $retry < MAX_RETRIES; $retry++) {
        $apiUrl = STATUS_URL . $idtrc;
        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            sleep(RETRY_DELAY);
            continue;
        }

        $data = json_decode($response, true);
        if (isset($data['Status'])) {
            error_log("[" . date('Y-m-d H:i:s') . "] Fetched status for idtrc {$idtrc}: {$data['Status']}");
            return strtolower($data['Status']);
        }

        error_log("Invalid response for idtrc {$idtrc}: {$response}");
        return null;
    }

    return null;
}

function updatePaymentStatus($mysqli, $payment_id, $idtrc, $status, $status_mappings)
{
    $mapped_status = $status_mappings[$status] ?? 'pending';

    $mysqli = createDatabaseConnection($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);

    $mysqli->begin_transaction();
    try {
        // Logic to determine the appropriate status
        if ($status === 'finished') {
            $mapped_status = 'paid';
        } elseif ($status === 'declined') {
            $mapped_status = 'declined';
        } elseif ($status === 'expired') {
            $mapped_status = 'timeout';
        } elseif ($status === 'in_exchange') {
            $mapped_status = 'in_exchange';
        } else {
            $mapped_status = $status_mappings[$status] ?? 'pending';
        }
    
        // Prepare the statement to update both status_trc and status
        $stmt = $mysqli->prepare("UPDATE user_payments SET status_trc = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
    
        if (!$stmt->bind_param("ssi", $status, $mapped_status, $payment_id)) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
    
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    
        $stmt->close();

        // If payment is marked as paid, process the payment benefits
        if ($mapped_status === 'paid') {
            // Get payment details
            $payment_query = "SELECT * FROM user_payments WHERE id = ?";
            $stmt = $mysqli->prepare($payment_query);
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $payment = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Get user details
            $user_query = "SELECT * FROM users WHERE id = ?";
            $stmt = $mysqli->prepare($user_query);
            $stmt->bind_param("i", $payment['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $data = json_decode($payment['data'], true);

            // Process affiliate tracking
            processAffiliate($mysqli, $payment, $user);

            // Process payment type benefits
            switch($payment['type']) {
                case 'premium':
                    processPremiumPayment($mysqli, $payment, $user, $data);
                    break;

                case 'tokens':
                    processTokenPayment($mysqli, $payment, $user, $data);
                    break;
            }
        }
    
        $mysqli->commit();
        error_log("Payment ID {$payment_id} updated with status_trc: {$status} and status: {$mapped_status}");
        
    } catch (Exception $e) {
        $mysqli->rollback();
        error_log("Failed to update payment ID {$payment_id}: " . $e->getMessage());
    }
}

function processAffiliate($mysqli, $payment, $user) {
    $affiliate_query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($affiliate_query);
    $stmt->bind_param("i", $user['affiliate_id']);
    $stmt->execute();
    $affiliate = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($affiliate) {
        $data = [
            'product' => $payment['product'],
            'amount' => $payment['amount'],
            'type' => $payment['type'],
            'payment_id' => $payment['id'],
            'method' => $payment['method']
        ];

        $worth = ($payment['amount'] / 100 * 15);

        // Get sub_id from lead
        $lead_query = "SELECT sub_id FROM affiliate_logs WHERE affiliate_id = ? AND user_id = ? AND type = 'lead' LIMIT 1";
        $stmt = $mysqli->prepare($lead_query);
        $stmt->bind_param("ii", $affiliate['id'], $user['id']);
        $stmt->execute();
        $lead = $stmt->get_result()->fetch_assoc();
        $sub_id = $lead ? $lead['sub_id'] : '';
        $stmt->close();

        // Insert affiliate log
        $log_query = "INSERT INTO affiliate_logs (affiliate_id, user_id, type, data, sub_id, worth) VALUES (?, ?, 'sale', ?, ?, ?)";
        $stmt = $mysqli->prepare($log_query);
        $json_data = json_encode($data);
        $stmt->bind_param("iissd", $affiliate['id'], $user['id'], $json_data, $sub_id, $worth);
        $stmt->execute();
        $stmt->close();
    }
}

function processPremiumPayment($mysqli, $payment, $user, $data) {
    if (!isset($data['premium_days'])) {
        throw new Exception("Premium days not specified");
    }

    $add = ($data['premium_days'] * 86400);
    $type = isset($data['buy']['type']) && $data['buy']['type'] == 'subline' ? 'subline' : 'mainline';
    $line_id = $data['buy']['line_id'];

    // Process default packages
    $defaults = getDefaultPackages();
    processPackages($mysqli, $user, $line_id, $type, $defaults, $data, $add);

    // Handle multiline settings
    $payment_data = json_decode($payment['data'], true);
    $multiline_value = $payment_data['buy']['multiline'];

    if ($type == 'subline') {
        updateSublinePremium($mysqli, $user['id'], $data['buy']['username'], $add, $multiline_value);
    } else {
        updateMainlinePremium($mysqli, $user['id'], $add, $multiline_value);
    }

    // Process referral bonus
    processReferralBonus($mysqli, $user);
}

function updateMainlinePremium($mysqli, $user_id, $add, $multiline_value) {
    // First verify the connection is still valid
    if ($mysqli->ping() === false) {
        // Reconnect if connection is lost
        $mysqli = createDatabaseConnection($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);
    }

    // Get current premium status with error handling
    $query = "SELECT premium_until FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        throw new Exception("Prepare failed for select: " . $mysqli->error);
    }

    if (!$stmt->bind_param("i", $user_id)) {
        throw new Exception("Bind failed for select: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed for select: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        throw new Exception("Get result failed: " . $stmt->error);
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    // Calculate new premium time
    $premium_until = $row['premium_until'];
    if ($premium_until == 0 || $premium_until < time()) {
        $premium_until = time();
    }
    
    $new_premium_until = $premium_until + $add;
    
    // Update premium status with error handling
    $update_query = "UPDATE user_lines SET premium_until = ?, multiline = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    if ($stmt === false) {
        throw new Exception("Prepare failed for update: " . $mysqli->error);
    }

    if (!$stmt->bind_param("isi", $new_premium_until, $multiline_value, $user_id)) {
        throw new Exception("Bind failed for update: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed for update: " . $stmt->error);
    }

    // Log successful update
    error_log("Successfully updated premium status for user {$user_id}. New premium_until: {$new_premium_until}, multiline: {$multiline_value}");
    
    $stmt->close();
    return true;
}

function updateSublinePremium($mysqli, $user_id, $username, $add, $multiline_value) {
    $query = "SELECT premium_until FROM user_lines WHERE user_id = ? AND username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("is", $user_id, $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $premium_until = $result['premium_until'];
    if ($premium_until == 0 || $premium_until < time()) {
        $premium_until = time();
    }
    
    $new_premium_until = $premium_until + $add;
    
    $update_query = "UPDATE user_lines SET premium_until = ?, multiline = ? WHERE user_id = ? AND username = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("isis", $new_premium_until, $multiline_value, $user_id, $username);
    $stmt->execute();
    $stmt->close();
}

function processReferralBonus($mysqli, $user) {
    if (!$user['referred_user_id']) {
        return;
    }

    // Get referred user details
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user['referred_user_id']);
    $stmt->execute();
    $referred_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$referred_user) {
        return;
    }

    // Check if this is the first premium payment
    $query = "SELECT COUNT(*) as count FROM user_payments 
              WHERE user_id = ? AND type = 'premium' AND status = 'paid'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $payment_count = $result['count'];
    $stmt->close();

    $premium_until = $referred_user['premium_until'];
    if ($premium_until == 0) {
        $premium_until = time();
    }

    // Add bonus days based on payment count
    $add = ($payment_count == 1) ? (3 * 86400) : (1 * 86400);
    $new_premium_until = $premium_until + $add;

    // Update referred user's premium time
    $update_query = "UPDATE users SET premium_until = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("ii", $new_premium_until, $referred_user['id']);
    $stmt->execute();
    $stmt->close();
}

function processTokenPayment($mysqli, $payment, $user, $data) {
    if (!isset($data['recorder_tokens'])) {
        throw new Exception("Recorder tokens not specified");
    }

    $update_query = "UPDATE users SET tokens = tokens + ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("ii", $data['recorder_tokens'], $user['id']);
    $stmt->execute();
    $stmt->close();
}

function getDefaultPackages() {
    return [
        [1, 'active', 'IPTV-DACH', true],
        [2, 'inactive', 'IPTV-DE HEVC', true],
        [3, 'active', 'IPTV-UK', true],
        [4, 'active', 'IPTV-Frankreich', true],
        [5, 'active', 'IPTV-Polen', true],
        [8, 'active', 'IPTV-Türkei', true],
        [9, 'active', 'IPTV-Rest von Europa', true],
        [10, 'active', 'IPTV-USA/Canada', true],
        [11, 'inactive', 'IPTV-Rest der Welt', true],
        [12, 'inactive', 'IPTV-World Sport', true],
        [24, 'active', 'IPTV-Music', true],
        [27, 'inactive', 'IPTV-XXX', true],
        [28, 'inactive', 'IPTV-VoD', true]
    ];
}

function processPackages($mysqli, $user, $line_id, $type, $defaults, $data, $add) {
    $exp_date = getExpirationDate($mysqli, $user, $line_id, $type);
    if (is_null($exp_date)) {
        throw new Exception("Invalid expiration date");
    }

    if ($exp_date < time()) {
        $exp_date = time();
    }
    $exp_date = ($exp_date + $add);

    $booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(1,3,4,5,8,9,10,24);

    foreach ($defaults as $default) {
        $packet = getExistingPacket($mysqli, $user['id'], $line_id, $default[0]);
        
        if ($packet) {
            updateExistingPacket($mysqli, $packet, $default[0], $booked_packages, $exp_date);
        } else {
            createNewPacket($mysqli, $user['id'], $line_id, $type, $default, $booked_packages, $exp_date);
        }
    }
}

function getExpirationDate($mysqli, $user, $line_id, $type) {
    if ($type == 'mainline') {
        return $user['premium_until'];
    }

    $query = "SELECT premium_until FROM user_lines WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $line_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $result ? $result['premium_until'] : null;
}

function getExistingPacket($mysqli, $user_id, $line_id, $bouquet_id) {
    $query = "SELECT * FROM user_packets WHERE user_id = ? AND line_id = ? AND bouquet_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iii", $user_id, $line_id, $bouquet_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function updateExistingPacket($mysqli, $packet, $bouquet_id, $booked_packages, $exp_date) {
    $status = in_array($bouquet_id, $booked_packages) ? 'active' : 'inactive';
    
    $query = "UPDATE user_packets SET status = ?, booked_until = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sii", $status, $exp_date, $packet['id']);
    $stmt->execute();
    $stmt->close();
}

function createNewPacket($mysqli, $user_id, $line_id, $type, $default, $booked_packages, $exp_date) {
    $status = in_array($default[0], $booked_packages) ? 'active' : 'inactive';
    
    $query = "INSERT INTO user_packets (user_id, name, status, line_type, line_id, bouquet_id, booked_until) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("isssiis", 
        $user_id,
        $default[2],
        $status,
        $type,
        $line_id,
        $default[0],
        $exp_date
    );
    $stmt->execute();
    $stmt->close();
}