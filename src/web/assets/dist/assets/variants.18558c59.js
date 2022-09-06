import{g as e}from"./useApolloClient.ea2bda77.js";const a=e`
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

`;export{l as V,a,i as b};
//# sourceMappingURL=variants.18558c59.js.map
