<?php

namespace percipiolondon\staff\helpers;

class HistoryMessages
{
    public const MESSAGES = [
        'employee_address_approved' => 'employee_address_approved',
        'employee_address_canceled' => 'employee_address_canceled',
        'employee_address_declined' => 'employee_address_declined',
        'employee_address_pending' => 'employee_address_pending',
        'employee_personal_details_approved' => 'employee_personal_details_approved',
        'employee_personal_details_canceled' => 'employee_personal_details_canceled',
        'employee_personal_details_declined' => 'employee_personal_details_declined',
        'employee_personal_details_pending' => 'employee_personal_details_pending',
        'employee_telephone_approved' => 'employee_telephone_approved',
        'employee_telephone_canceled' => 'employee_telephone_canceled',
        'employee_telephone_declined' => 'employee_telephone_declined',
        'employee_telephone_pending' => 'employee_telephone_pending',
        'system_user_activate' => 'system_user_active',
        'system_user_set_password' => 'system_user_set_password',
    ];

    public static function message(string $type, string $detail = null, string $status = null): ?string
    {
        return self::MESSAGES[$type.($detail ? '_'.$detail : '').($status ? '_'.$status : '')] ?? null;
    }
}