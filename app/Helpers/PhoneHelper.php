<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * PhoneHelper - Utility untuk normalisasi nomor telepon Indonesia
 * 
 * DeepCode: Centralized phone number handling.
 * DeepReasoning: Digunakan di FonnteService, TrackingController, dan tempat lain.
 */
class PhoneHelper
{
    /**
     * Normalize phone number untuk format Indonesia (62xxx).
     * 
     * DeepLogic: 
     * - Remove non-numeric characters
     * - Convert 0xxx to 62xxx
     * - Keep 62xxx as is
     * 
     * @param string $phone
     * @return string Normalized phone (62xxx format)
     */
    public static function normalize(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, assume it's already valid or add 62
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Normalize phone number untuk format lokal (0xxx).
     * 
     * DeepLogic: Convert 62xxx to 0xxx for local display/comparison.
     * 
     * @param string $phone
     * @return string Normalized phone (0xxx format)
     */
    public static function normalizeLocal(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle +62 prefix
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        // If doesn't start with 0, add it
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }
        
        return $phone;
    }

    /**
     * Format phone number untuk display (dengan spasi).
     * 
     * DeepUX: Format 0812-3456-7890 lebih mudah dibaca.
     * 
     * @param string $phone
     * @return string Formatted phone
     */
    public static function format(string $phone): string
    {
        $phone = self::normalizeLocal($phone);
        
        // Format: 0812-3456-7890
        if (strlen($phone) >= 11) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }
        
        return $phone;
    }

    /**
     * Validate Indonesian phone number.
     * 
     * DeepValidation: Check if phone number is valid Indonesian format.
     * 
     * @param string $phone
     * @return bool
     */
    public static function isValid(string $phone): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Indonesian phone: 10-13 digits, starts with 0 or 62
        if (str_starts_with($phone, '0')) {
            return strlen($phone) >= 10 && strlen($phone) <= 13;
        }
        
        if (str_starts_with($phone, '62')) {
            return strlen($phone) >= 11 && strlen($phone) <= 14;
        }
        
        return false;
    }
}
