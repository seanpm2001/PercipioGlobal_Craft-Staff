import { gql } from 'graphql-tag'

export const VARIANT = gql`
    query BenefitVariant($id: [QueryArgument]) {
        BenefitVariant(id: $id) {
            id
            name
            policy {
                  id
                  internalCode
                  providerId
                  employerId
                  benefitTypeId
                  status
                  policyName
                  policyNumber
                  policyHolder
                  policyStartDate
                  policyRenewalDate
                  paymentFrequency
                  commissionRate
                  description
            }
            totalRewardsStatement {
                  title
                  monetaryValue
                  startDate
                  endDate
            }
            employees {
                  id
                  personalDetails{
                        firstName
                        lastName
                  }
            }
        }
    }
`

export const VARIANTS = gql`
    query BenefitVariants($employeeId: Int, $policyId: Int) {
        BenefitVariants(policyId: $policyId, employeeId: $employeeId, orderBy: "name ASC") {
            id
            name
            policy {
                  id
                  internalCode
                  providerId
                  employerId
                  benefitTypeId
                  status
                  policyName
                  policyNumber
                  policyHolder
                  policyStartDate
                  policyRenewalDate
                  paymentFrequency
                  commissionRate
                  description
            }
            totalRewardsStatement {
                  title
                  monetaryValue
                  startDate
                  endDate
            }
            employees {
                  id
                  personalDetails{
                        firstName
                        lastName
                  }
            }
        }
    }
`

export const VARIANT_ELIGIBLE_EMPLOYEES = gql`
    query BenefitVariantEligibleEmployees($policyId: Int!) {
          BenefitVariantEligibleEmployees(policyId: $policyId) {
                id,
                personalDetails {
                      firstName
                      lastName
                }
          }
    }
`

export const ADD_VARIANT_EMPLOYEE = gql`
    mutation AddEmployee($employeeId: Int!, $variantId: Int!) {
          AddEmployee(employeeId: $employeeId, variantId: $variantId) {
                id,
                personalDetails {
                    firstName
                    lastName
                }
          }
    }
`

export const REMOVE_VARIANT_EMPLOYEE = gql`
    mutation RemoveEmployee($employeeId: Int!, $variantId: Int!) {
          RemoveEmployee(employeeId: $employeeId, variantId: $variantId) {
                id
          }
    }

`
