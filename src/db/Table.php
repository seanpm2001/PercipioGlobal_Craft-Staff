<?php

namespace percipiolondon\staff\db;

abstract class Table
{
    const EMPLOYERS = "{{%staff_employers}}";
    const EMPLOYEES = "{{%staff_employees}}";
    const USERS = "{{%staff_users}}";
    const PAYRUN = "{{%staff_payrun}}";
    const PAYRUN_LOG = "{{%staff_log_payrun}}";
    const PAYRUN_ENTRIES = "{{%staff_payrunentries}}";
    const PERMISSIONS = "{{%staff_permissions}}";
    const PERMISSIONS_USERS = "{{%staff_permissions_users}}";
    const REQUESTS = "{{%staff_requests}}";
    const HISTORY = "{{%staff_history}}";
    const PERSONAL_DETAILS = "{{%staff_personal_details}}";
}
