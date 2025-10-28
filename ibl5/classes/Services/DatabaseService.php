<?php

namespace Services;

/**
 * Database Utility Service
 *
 * Provides shared database utility functions, such as string escaping.
 */
class DatabaseService
{
    /**
     * Escapes a string for SQL queries using mysqli_real_escape_string if available,
     * otherwise falls back to sql_escape_string or addslashes.
     *
     * @param object $db Database connection object
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeString($db, string $string): string
    {
        if (isset($db->db_connect_id) && $db->db_connect_id) {
            return mysqli_real_escape_string($db->db_connect_id, $string);
        }
        if (method_exists($db, 'sql_escape_string')) {
            return $db->sql_escape_string($string);
        }
        return addslashes($string);
    }
}