<?php

namespace percipiolondon\staff\helpers;

class NotificationMessage
{
    public const MESSAGES = [
        'employee_address_approved' => [
            'email' => 'email_employee_address_approved',
            'notification' => 'employee_address_approved'
        ],
        'employee_address_declined' => [
            'email' => 'email_employee_address_declined',
            'notification' => 'employee_address_declined'
        ],
        'employee_address_pending' => [
            'admin' => 'email_admin_employee_address_pending',
            'notification' => 'employee_address_pending'
        ],
        'employee_personal_details_approved' => [
            'email' => 'email_employee_personal_details_approved',
            'notification' => 'employee_personal_details_approved'
        ],
        'employee_personal_details_declined' => [
            'email' => 'email_employee_personal_details_declined',
            'notification' => 'employee_personal_details_declined'
        ],
        'employee_personal_details_pending' => [
            'admin' => 'email_admin_employee_personal_details_pending',
            'notification' => 'employee_personal_details_pending'
        ],
        'employee_telephone_approved' => [
            'email' => 'email_employee_telephone_approved',
            'notification' => 'employee_telephone_approved'
        ],
        'employee_telephone_declined' => [
            'email' => 'email_employee_telephone_declined',
            'notification' => 'employee_telephone_declined'
        ],
        'employee_telephone_pending' => [
            'admin' => 'email_admin_employee_telephone_pending',
            'notification' => 'employee_telephone_pending'
        ],
        'payroll_payslip' => [
            'email' => 'email_payroll_payslip',
            'notification' => 'payroll_payslip'
        ],
    ];

    public static function getAdminEmail(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[self::getType($type, $detail, $status)]['admin'] ?? null;
    }

    public static function getEmail(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[self::getType($type, $detail, $status)]['email'] ?? null;
    }

    public static function getNotification(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[self::getType($type, $detail, $status)]['notification'] ?? null;
    }

    public static function getType(string $type, string $detail = null, string $status = null): ?string
    {
        return $type.($detail ? '_'.$detail : '').($status ? '_'.$status : '');
    }
}