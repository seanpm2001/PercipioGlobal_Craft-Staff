import{d as c,o as t,c as o,F as d,r as m,a as n,t as i,u as p,g as u,h as f,p as y}from"./vue.esm-bundler.9a334c3a.js";import{g,u as h,d as _,D as b}from"./useApolloClient.63c21703.js";const I=g`
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
        }
    }
`,x={class:"grid sm:grid-cols-2 md:grid-cols-3"},V={class:"bg-white shadow rounded-lg overflow-hidden no-underline flex flex-col"},w={class:"flex-grow flex flex-col w-full justify-start m-0 mr-0 box-border p-6",style:{"margin-right":"0!important"}},D={class:"text-base mb-1 pt-0 w-full",style:{"margin-right":"0!important"}},B={class:"font-light text-4xl text-indigo-900 mt-2 mb-4 w-full",style:{"margin-right":"0!important"}},R=c({__name:"Variants",setup(l){const{result:s,loading:A}=h(I);return(C,N)=>{var a;return t(),o("div",x,[(t(!0),o(d,null,m((a=p(s))==null?void 0:a.BenefitVariants,e=>{var r;return t(),o("article",V,[n("header",w,[n("h2",D,i(e.name),1),n("h3",B,i((r=e==null?void 0:e.totalRewardsStatement)==null?void 0:r.monetaryValue),1)])])}),256))])}}}),S=async()=>u({setup(){y(b,_)},render:()=>f(R)}).mount("#benefit-variants-container");S().then(()=>{console.log()});
//# sourceMappingURL=benefitVariants.eb147283.js.map
