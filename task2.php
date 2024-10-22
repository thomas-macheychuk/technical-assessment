<?php
// Database configuration for SQLite and MySQL
// Note: storing database credentials directly in constants has serious security implications, therefore in real-life scenarios,
// they should be stored in env, cfg outside the web root, or in secret management services (for instance, AWS Secrets Manager)
const DB_TYPE = 'sqlite'; // 'sqlite' or 'mysql'
const DB_PATH = 'your_sqlite_db'; // For SQLite
const DB_HOST = 'localhost'; // For MySQL
const DB_NAME = 'your_database_name'; // For MySQL
const DB_USER = 'your_username'; // For MySQL
const DB_PASS = 'your_password'; // For MySQL


/**
 * Checks if an IPv4 address is within a list of allowed IP ranges stored in a database.
 *
 * Supports SQLite and MySQL databases.
 *
 * The list of IP ranges can include:
 * - Single IP addresses (e.g., 192.168.1.1)
 * - IP address ranges (e.g., 192.168.1.0-192.168.1.255)
 * - CIDR ranges (e.g., 192.168.1.0/24)
 *
 * @param string $ip The IPv4 address to check.
 * @return bool True if the IP address is within the allowed list, false otherwise.
 */
function isIpAllowed(string $ip): bool
{
    $ipLong = ip2long($ip);
    if ($ipLong === false) {
        return false; // Invalid IP address
    }

    try {
        // Connect to the database based on DB_TYPE
        if (DB_TYPE === 'sqlite') {
            $db = new PDO('sqlite:' . DB_PATH);
        } elseif (DB_TYPE === 'mysql') {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        } else {
            throw new PDOException("Unsupported database type: " . DB_TYPE);
        }

        $stmt = $db->query("SELECT range FROM ip_ranges");
        $allowedIpRanges = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($allowedIpRanges as $range) {
            if (strpos($range, '/') !== false) {
                // CIDR range
                if (cidrMatch($ip, $range)) {
                    return true;
                }
            } elseif (strpos($range, '-') !== false) {
                // IP range
                [$start, $end] = explode('-', $range);
                $startLong = ip2long($start);
                $endLong = ip2long($end);
                if ($startLong !== false && $endLong !== false && $ipLong >= $startLong && $ipLong <= $endLong) {
                    return true;
                }
            } else {
                // Single IP address
                if ($ip === $range) {
                    return true;
                }
            }
        }

        return false;

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        return false;
    }
}

/**
 * Checks if an IP address is within a CIDR range.
 *
 * This function converts the IP address and the CIDR range to their long integer representations
 * and then uses bitwise operations to determine if the IP address falls within the subnet defined by the CIDR range.
 *
 * @param string $ip The IPv4 address to check.
 * @param string $cidr The CIDR range.
 * @return bool True if the IP address is within the CIDR range, false otherwise.
 */
function cidrMatch(string $ip, string $cidr): bool
{
    [$subnet, $mask] = explode('/', $cidr);
    $maskLong = ~((1 << (32 - $mask)) - 1);
    $subnetLong = ip2long($subnet);
    $ipLong = ip2long($ip);

    return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
}

