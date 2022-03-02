# Database

Suffixed every FK linkage with `Id` if it links to another table ID

## Table::EMPLOYEES
### personalDetailsId (PersonalDetails)
Query on employeeId from within staffology call
### employmentDetailsId (EmploymentDetails)
Query on employeeId from within staffology call
### autoEnrolmentId (AutoEnrolment)
Query on employeeId from within staffology call
### leaveSettingsId (LeaveSettings)
Query on employeeId from within staffology call
### rightToWorkId (RightToWork)
Query on employeeId from within staffology cal
### bankDetails (BankDetails)
Query on accountNumber from within staffology call
### payOptionsId (PayOptions)
Query on employeeId from within staffology call

## Table::EMPLOYERS
### addressId (Address)
Query on employeeId from within staffology call
### defaultPayOptionsId (PayOptions)
Query on employerId from within staffology call
### bankDetails (BankDetails)
Query on accountNumber from within staffology call
### hmrcDetailsId (HmrcDetails)
Query on employerId from within staffology call
### defaultPensionId (PensionSelection)
Query on employerId from within staffology call
### rtiSubmissionSettingsId (RtiSubmissionSettings)
Query on employerId from within staffology call
### autoEnrolmentSettingsId (AutoEmploymentSettings)
Query on employerId from within staffology call
### leaveSettingsId (LeaveSettings)
Query on employerId from within staffology call
### settingsId (EmployerSettings)
Query on employerId from within staffology call
### umbrellaSettingsId (UmbrellaSettings)
Query on employerId from within staffology call
### RELATIONSHIP TABLES
#### customPayCodes
PAY_CODES: Query on employerId from within staffology call

## Table::PAYRUN
### totalsId (PayRunTotals)
Query on payRunId from within staffology call
### employerId (Employer)

## Table::PAYRUN_ENRIES
### priorPayrollCodeId (PayCode)
Query on code from within staffology call
### payOptionsId (PayOptions)
Query on code from within staffology call
### pensionSummaryId (PensionSummary)
Query on payRunEntryId from within payRunEntry
### personalDetails (PersonalDetails)
Query on employeeId
### periodOverrides (x)
There's a ValueOverride created where the ValueOverride.id is a FK link to PAYRUN_ENTRIES with `field` value periodOverrides
### totalsYtdId (PayRunTotals)
There's no UUID to query on. Check on which combination of fields to query on to make the query unique
### totalsYtdOverrides (x)
There's a ValueOverride created where the ValueOverride.id is a FK link to PAYRUN_ENTRIES with `field` value totalsYtdOverrides
### nationalInsuranceCalculationId (NationalInsuranceCalculation)
There's no UUID to query on. Check on which combination of fields to query on to make the query unique
### umbrellaPaymentId (UmbrellaPayment)
There's no UUID to query on. Check on which combination of fields to query on to make the query unique
### RELATIONSHIP TABLES
#### periodOverridesId
VALUE_OVERRIDE: Query on payRunEntryId
#### totalsYtdOverridesId
VALUE_OVERRIDE: Query on payRunEntryId
### QUESTIONS
- PAYRUN_ENTRIES.note: add this into the NOTE table or leave it as Stafology in a string?

--------------------------------------------------

## Table::PAY_OPTIONS
### employerId (Employer)
Can be null if it's about an employee or pay run entry. Query on employerId from within our system
### employeeId (Employee)
Can be null if it's about an employer or pay run entry. Query on employeeId from within our system
### payRunEntryId (PayRunEntry)
Can be null if it's about an employer or employee. Query on payRunEntryId from within staffology
### taxAndNiId (TaxAndNi)
Query on payOptionId from within staffology
### fpsFieldsId (TaxAndNi)
Query on payOptionId from within staffology
### RELATIONSHIP TABLES
#### regularPayLinesId
PAY_LINES: Query on payOptionsId from within staffology call

## Table::PERSONAL_DETAILS
### addressId (Address)
Query on employerId from within staffology call

## Table::EMPLOYMENT_DETAILS
### posts
This is added in the ItemRelations with employmentDetailId

## Table::FAMILY_DETAILS
This links up to the family details of an employee

## Table::VALUE_OVERRIDE
### pensionId
There's a UUID (staffologyId) to query on.
### field
There are two fields in PAYRUN_ENTRIES that are linked to this field. So we need the type to define for which field in the PAYRUND_ENTRIES it should be
### QUESTIONS
- VALUE_OVERRIDE.note: add this into the NOTE table or leave it as Stafology in a string?

## Table::RTI_AGENT
### address
There's no UUID to query on. Check on which combination of fields to query on to make the query unique
### contact
There's no UUID to query on. Check on which combination of fields to query on to make the query unique