import{d as g,o as t,c as o,u as l,b as u,F as y,r as _,a as s,t as i,g as w,h as V,p as b}from"./vue.esm-bundler.9a334c3a.js";import{g as I,u as x,d as C,D}from"./useApolloClient.63c21703.js";const B=I`
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
`,N={key:0,class:"animate-spin mr-3 h-5 w-5 text-indigo-900",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},k=s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),A=s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),$=[k,A],R={key:1,class:"grid sm:grid-cols-2 md:grid-cols-3"},S=["href"],q={class:"text-base mb-1 pt-0 w-full",style:{"margin-right":"0!important"}},F={class:"font-light text-4xl text-indigo-900 mt-2 mb-4 w-full",style:{"margin-right":"0!important"}},z=g({__name:"Variants",setup(h){const{result:n,loading:f}=x(B);return(T,E)=>{var a;return t(),o(y,null,[l(f)?(t(),o("svg",N,$)):u("",!0),l(n)?(t(),o("div",R,[(t(!0),o(y,null,_((a=l(n))==null?void 0:a.BenefitVariants,e=>{var r,c,d,m,p;return t(),o("a",{href:`/admin/staff-management/benefits/policy/${(r=e==null?void 0:e.policy)==null?void 0:r.id}/variants/${e.id}`,class:"bg-white shadow rounded-lg overflow-hidden no-underline p-4"},[s("h2",q,i(e.name),1),s("h3",F," \xA3 "+i((d=(c=e==null?void 0:e.totalRewardsStatement)==null?void 0:c.monetaryValue)!=null?d:"-"),1),s("p",null,i((p=(m=e==null?void 0:e.employees)==null?void 0:m.length)!=null?p:0)+" employees attached",1)],8,S)}),256))])):u("",!0)],64)}}}),H=async()=>w({setup(){b(D,C)},render:()=>V(z)}).mount("#benefit-variants-container");H().then(()=>{console.log()});
//# sourceMappingURL=benefitVariants.e23c7306.js.map
