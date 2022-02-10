<?php

namespace percipiolondon\staff\db;

abstract class Table
{
    const ADDRESSES = "{{%staff_addresses}}";
    const AUTO_ENROLMENT = "{{%staff_autoenrolment}}";
    const AUTO_ENROLMENT_ASSESSMENT = "{{%staff_autoenrolment_assessment}}";
    const AUTO_ENROLMENT_ASSESSMENT_ACTION = "{{%staff_autoenrolment_assessment_action}}";
    const AUTO_ENROLMENT_SETTINGS = "{{%staff_autoenrolment_settings}}";
    const BANK_DETAILS = "{{%staff_bankdetails}}";
    const CIS_DETAILS = "{{%staff_cisdetails}}";
    const CIS_PARTNERSHIP = "{{%staff_cis_partnership}}";
    const CIS_SUBCONTRACTOR = "{{%staff_cis_subcontractor}}";
    const CIS_VERIFICATION_DETAILS = "{{%staff_cis_verificationdetails}}";
    const COUNTRIES = "{{%staff_countries}}";
    const CUSTOM_PAY_CODES = "{{%staff_custompaycodes}}";
    const DEPARTMENT = "{{%staff_department}}";
    const DIRECTORSHIP_DETAILS = "{{%staff_directorshipdetails}}";
    const EMPLOYEES = "{{%staff_employees}}";
    const EMPLOYERS = "{{%staff_employers}}";
    const EMPLOYER_SETTINGS = "{{%staff_employersettings}}";
    const EMPLOYMENT_DETAILS = "{{%staff_employmentdetails}}";
    const FAMILY_DETAILS = "{{%staff_familyDetails}}";
    const FPS_FIELDS = "{{%staff_fpsfields}}";
    const HISTORY = "{{%staff_history}}";
    const HMRC_DETAILS = "{{%staff_hrmcdetails}}";
    const ITEMS = "{{%staff_items}}";
    const ITEM_RELATIONS = "{{%staff_itemrelations}}";
    const LEAVE_SETTINGS = "{{%staff_leavesettings}}";
    const LEAVER_DETAILS = "{{%staff_leaverdetails}}";
    const NATIONAL_INSURANCE_CALCULATION = "{{%staff_nationalinsurancecalculations}}";
    const NOTE = "{{%staff_notes}}";
    const OVERSEAS_EMPLOYER_DETAILS = "{{%staff_oveseasemployerdetails}}";
    const PAY_CODES = "{{%staff_paycodes}}";
    const PAY_OPTIONS = "{{%staff_payoptions}}";
    const PAYLINES = "{{%staff_paylines}}";
    const PAYRUN = "{{%staff_payrun}}";
    const PAYRUN_ENTRIES = "{{%staff_payrunentries}}";
    const PAYRUN_LOG = "{{%staff_payrunlogs}}";
    const PAYRUN_TOTALS = "{{%staff_payruntotals}}";
    const PENSION = "{{%staff_pension}}";
    const PENSION_ADMINISTRATOR = "{{%staff_pensionadministrator}}";
    const PENSION_PROVIDER = "{{%staff_pensionprovider}}";
    const PENSION_SCHEME = "{{%staff_pensionscheme}}";
    const PENSION_SELECTION = "{{%staff_pensionselection}}";
    const PENSION_SUMMARY = "{{%staff_pensionsummary}}";
    const PENSIONER_PAYROLL = "{{%staff_pensionerpayroll}}";
    const PERMISSIONS = "{{%staff_permissions}}";
    const PERMISSIONS_USERS = "{{%staff_permissions_users}}";
    const PERSONAL_DETAILS = "{{%staff_personal_details}}";
    const REQUESTS = "{{%staff_requests}}";
    const RIGHT_TO_WORK = "{{%staff_righttowork}}";
    const RTI_AGENT = "{{%staff_rti_agent}}";
    const RTI_CONTACT = "{{%staff_rti_contact}}";
    const RTI_EMPLOYEE_ADDRESS = "{{%staff_rti_employeeaddress}}";
    const RTI_EMPLOYEE_NAME = "{{%staff_rti_employeename}}";
    const RTI_SUBMISSION_SETTINGS = "{{%staff_rti_submissionsettings}}";
    const STARTER_DETAILS = "{{%staff_starterdetails}}";
    const TAX_AND_NI = "{{%staff_taxandni}}";
    const TEACHER_PENSION_DETAILS = "{{%staff_teacherpensiondetails}}";
    const TIERED_PENSION_RATE = "{{%staff_tieredpensionrate}}";
    const UMBRELLA_PAYMENT = "{{%staff_umbrellaspayment}}";
    const UMBRELLA_SETTINGS = "{{%staff_umbrellasettings}}";
//    const USERS = "{{%staff_users}}";
    const VALUE_OVERRIDE = "{{%staff_valueoverride}}";
    const WORKER_GROUP = "{{%staff_workergroup}}";
}
