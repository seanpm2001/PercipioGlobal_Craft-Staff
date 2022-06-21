import{g as x,d as m,o as r,e as y,f as t,t as o,i as d,r as f,c as i,F as u,j as g,a as c,k as h,b as v,p as b,h as _,D as w}from"./vendor.638d3191.js";import{L as $,d as k}from"./useApolloClient.248cbc93.js";const C=x`
    query Employers {
        employers(orderBy: "name desc") {
            id
            crn
            name
            employeeCount
            currentYear
            logoUrl
            dateSynced:dateUpdated @formatDateTime(format:"Y-m-d H:i")
            currentPayRun{
                taxYear
                period
            }
        }
    }
`,R=["href","title"],E={class:"flex items-center col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6"},L={class:"object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0"},P=["src"],S={style:{"margin-bottom":"0"}},Y={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},B={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},j={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},D={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},N=m({props:{employer:null},setup(e){return(l,a)=>{var s,n,p;return e.employer?(r(),y("a",{key:0,href:`/admin/staff-management/pay-runs/${e.employer.id}/${(s=e.employer.currentPayRun)==null?void 0:s.taxYear}`,title:`Go to pay runs of ${e.employer.name}`,class:"grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[t("div",E,[t("div",L,[t("img",{src:e.employer.logoUrl,class:"w-full h-full"},null,8,P)]),t("span",S,o(e.employer.name),1)]),t("div",Y,o(e.employer.crn?e.employer.crn:"-"),1),t("div",B,o(e.employer.employeeCount),1),t("div",j,o((n=e.employer.currentPayRun)==null?void 0:n.taxYear)+" / "+o((p=e.employer.currentPayRun)==null?void 0:p.period),1),t("div",D,o(e.employer.dateSynced),1)],8,R)):d("",!0)}}}),U=m({props:{employers:Object},setup(e){return(l,a)=>(r(!0),y(u,null,f(e.employers,s=>(r(),i(N,{key:s.id,employer:s},null,8,["employer"]))),128))}}),V=t("div",{class:"sm:flex"},[t("div",{class:"sm:flex-auto"},[t("h1",{class:"text-xl font-semibold text-gray-900"},"Employers"),t("p",{class:"mt-2 text-sm text-gray-700"},"Select a company below to begin bulk pay run management.")])],-1),q={class:"mt-8 flex flex-col w-full"},A={class:"-my-2 overflow-x-auto w-full"},F={class:"inline-block min-w-full py-2 align-middle"},O={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},G=h('<div class="grid grid-cols-6 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Company</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">CRN</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Employee count</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Pay Run</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last synced</div></div>',1),H=m({setup(e){const{result:l,loading:a}=g(C);return(s,n)=>(r(),y(u,null,[V,t("div",q,[t("div",A,[t("div",F,[t("div",O,[G,c(a)?(r(),i($,{key:0})):d("",!0),c(l)?(r(),i(U,{key:1,employers:c(l).employers},null,8,["employers"])):d("",!0)])])])])],64))}}),M=async()=>v({setup(){b(w,k)},render:()=>_(H)}).mount("#employer-container");M().then(()=>{console.log()});
//# sourceMappingURL=employers.86e3cf6d.js.map
