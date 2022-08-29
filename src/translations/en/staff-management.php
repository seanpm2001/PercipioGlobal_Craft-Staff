<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

/**
 * staff-management en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('staff-management.', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
return [
    'staff-management plugin loaded' => 'The Hub plugin loaded!',

    //HISTORY
    'employee_address_approved' => 'This is the translated message for address approved.',
    'employee_address_canceled' => 'This is the translated message for address canceled.',
    'employee_address_declined' => 'This is the translated message for address declined.',
    'employee_address_pending' => 'This is the translated message for address pending.',
    'employee_personal_details_approved' => 'This is the translated message for personal details approved.',
    'employee_personal_details_canceled' => 'This is the translated message for personal details canceled.',
    'employee_personal_details_declined' => 'This is the translated message for personal details declined.',
    'employee_personal_details_pending' => 'This is the translated message for personal details pending.',
    'employee_telephone_approved' => 'This is the translated message for telephone approved.',
    'employee_telephone_canceled' => 'This is the translated message for telephone canceled.',
    'employee_telephone_declined' => 'This is the translated message for telephone declined.',
    'employee_telephone_pending' => 'This is the translated message for telephone pending.',
    'payroll_payslip' => 'There is a new payslip available',
    'system_user_active' => 'Your account has been activated.',
    'system_user_set_password' => 'A new password has been set.',

    //SETTINGS
    'notifications:app' => 'Notification emails about the app',
    'notifications:benefit' => 'Notification emails about your benefits',
    'notifications:employee' => 'Notification emails about your personal details',
    'notifications:payroll' => 'Notification emails about your payroll',
    'notifications:pension' => 'Notification emails about your pension',
    'notifications:system' => 'Notification emails about the system',

    //EMAILS
    'email_admin_employee_address_pending' => 'Dear {name}<br><br>There is an incoming request to update address information. View request in <a href="{adminUrl}">the admin panel</a><br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_address_approved' => 'Dear {name}<br><br>Your request to update your address information has been approved and can now be viewed on the Harding Hub.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_address_declined' => 'Dear {name}<br><br>Your request to update your address information has been declined. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_address_pending' => 'Dear {name}<br><br>Your request to update your address information has been sent to our team. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_admin_employee_personal_details_pending' => 'Dear {name}<br><br>There is an incoming request to update personal information. View request in <a href="{adminUrl}">the admin panel</a><br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_personal_details_approved' => 'Dear {name}<br><br>Your request to update your personal information has been approved and can now be viewed on the Harding Hub.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_personal_details_declined' => 'Dear {name}<br><br>Your request to update your personal information has been declined. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_personal_details_pending' => 'Dear {name}<br><br>Your request to update your personal information has sent to our team. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_admin_employee_telephone_pending' => 'Dear {name}<br><br>There is an incoming request to update contact information. View request in <a href="{adminUrl}">the admin panel</a><br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_telephone_approved' => 'Dear {name}<br><br>Your request to update your contact information has been approved and can now be viewed on the Harding Hub.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_telephone_declined' => 'Dear {name}<br><br>Your request to update your contact information has been declined. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_employee_telephone_pending' => 'Dear {name}<br><br>Your request to update your contact information has been sent to our team. More details can be found on the Harding Hub in the history log.<br><br>Kind regards,<br>The Harding Hub team',
    'email_payroll_payslip' => 'Dear {name}<br><br>There is a new payslip available on the Harding Hub. You can view it in the Payroll History.<br><br>Kind regards,<br>The Harding Hub team',
];
