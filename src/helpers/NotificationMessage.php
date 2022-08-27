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
        'employee_personal_details_approved' => [
            'email' => 'email_employee_personal_details_approved',
            'notification' => 'employee_personal_details_approved'
        ],
        'employee_personal_details_declined' => [
            'email' => 'email_employee_personal_details_declined',
            'notification' => 'employee_personal_details_declined'
        ],
        'employee_telephone_approved' => [
            'email' => 'email_employee_telephone_approved',
            'notification' => 'employee_telephone_approved'
        ],
        'employee_telephone_declined' => [
            'email' => 'email_employee_telephone_declined',
            'notification' => 'employee_telephone_declined'
        ],
        'payroll_payslip' => [
            'email' => 'email_payroll_payslip',
            'notification' => 'payroll_payslip'
        ],
    ];

    public static function getEmail(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[$type.($detail ? '_'.$detail : '').($status ? '_'.$status : '')]['email'] ?? null;
    }

    public static function getNotification(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[$type.($detail ? '_'.$detail : '').($status ? '_'.$status : '')]['notification'] ?? null;
    }
}