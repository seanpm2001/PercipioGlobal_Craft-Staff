<?php

namespace percipiolondon\staff\db;

abstract class Table
{
    const ADDRESSES = "{{%staff_addresses}}";
    const BANK_DETAILS = "{{%staff_bankdetails}}";
    const COUNTRIES = "{{%staff_countries}}";
    const EMPLOYEES = "{{%staff_employees}}";
    const EMPLOYERS = "{{%staff_employers}}";
    const EMPLOYMENT_DETAILS = "{{%staff_employmentdetails}}"
    const HISTORY = "{{%staff_history}}";
    const LEAVE_SETTINGS = "{{%staff_leavesettings}}";
    const PAYRUN = "{{%staff_payrun}}";
    const PAYRUN_LOG = "{{%staff_log_payrun}}";
    const PAYRUN_ENTRIES = "{{%staff_payrunentries}}";
    const PERMISSIONS = "{{%staff_permissions}}";
    const PERMISSIONS_USERS = "{{%staff_permissions_users}}";
    const PERSONAL_DETAILS = "{{%staff_personal_details}}";
    const REQUESTS = "{{%staff_requests}}";
    const USERS = "{{%staff_users}}";
}
