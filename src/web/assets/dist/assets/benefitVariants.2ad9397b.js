import{d as f,o as t,c as o,u as a,b as p,F as u,r as g,a as s,t as l,g as _,h as w,p as V}from"./vue.esm-bundler.9a334c3a.js";import{g as b,u as I,d as x,D as C}from"./useApolloClient.63c21703.js";const D=b`
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
`,B={key:0,class:"animate-spin mr-3 h-5 w-5 text-indigo-900",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},N=s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),k=s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),A=[N,k],R={key:1,class:"grid sm:grid-cols-2 md:grid-cols-3"},S=["href"],$={class:"text-base mb-1 pt-0 w-full",style:{"margin-right":"0!important"}},q={class:"font-light text-4xl text-indigo-900 mt-2 mb-4 w-full",style:{"margin-right":"0!important"}},F=f({__name:"Variants",setup(y){const{result:n,loading:h}=I(D);return(H,T)=>{var i;return t(),o(u,null,[a(h)?(t(),o("svg",B,A)):p("",!0),a(n)?(t(),o("div",R,[(t(!0),o(u,null,g((i=a(n))==null?void 0:i.BenefitVariants,e=>{var r,c,d,m;return t(),o("a",{href:`/admin/staff-management/benefits/variant/${e.id}`,class:"bg-white shadow rounded-lg overflow-hidden no-underline p-4"},[s("h2",$,l(e.name),1),s("h3",q," \xA3 "+l((c=(r=e==null?void 0:e.totalRewardsStatement)==null?void 0:r.monetaryValue)!=null?c:"-"),1),s("p",null,l((m=(d=e==null?void 0:e.employees)==null?void 0:d.length)!=null?m:0)+" employees attached",1)],8,S)}),256))])):p("",!0)],64)}}}),z=async()=>_({setup(){V(C,x)},render:()=>w(F)}).mount("#benefit-variants-container");z().then(()=>{console.log()});
//# sourceMappingURL=benefitVariants.2ad9397b.js.map
