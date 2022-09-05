import{d as n,t as o,u as a,g as i,h as r,p as s}from"./vue.esm-bundler.9a334c3a.js";import{g as p,u as l,d as c,D as d}from"./useApolloClient.63c21703.js";const y=p`
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
        }
    }
`,m=n({__name:"Variants",setup(t){const{result:e,loading:f}=l(y);return(I,_)=>o(a(e))}}),u=async()=>i({setup(){s(d,c)},render:()=>r(m)}).mount("#benefit-variants-container");u().then(()=>{console.log()});
//# sourceMappingURL=benefitVariants.f67535ee.js.map
