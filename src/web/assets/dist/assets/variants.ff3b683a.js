import{g as e}from"./useApolloClient.457b3af6.js";const a=e`
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
`,i=e`
    query BenefitVariants($employeeId: Int, $policyId: Int) {
        BenefitVariants(policyId: $policyId, employeeId: $employeeId) {
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
`,l=e`
    query BenefitVariantEligibleEmployees($policyId: Int!) {
          BenefitVariantEligibleEmployees(policyId: $policyId) {
                id,
                personalDetails {
                      firstName
                      lastName
                }
          }
    }

`,o=e`
    mutation AddEmployee($employeeId: Int!, $variantId: Int!) {
          AddEmployee(employeeId: $employeeId, variantId: $variantId) {
                id,
                personalDetails {
                    firstName
                    lastName
                }
          }
    }

`;export{o as A,l as V,a,i as b};
//# sourceMappingURL=variants.ff3b683a.js.map
