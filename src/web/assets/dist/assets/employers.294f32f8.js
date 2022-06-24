import{d as m,f as r,i as y,j as t,t as o,q as i,y as x,z as d,F as u,u as c,A as f,x as g,h,B as v}from"./vue.esm-bundler.23929e85.js";import{g as _,u as b,L as w,d as $,D as k}from"./useApolloClient.e1e0b3e4.js";const C=_`
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
`,E=["href","title"],R={class:"flex items-center col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6"},B={class:"object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0"},L=["src"],P={style:{"margin-bottom":"0"}},S={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},Y={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},j={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},D={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},N=m({__name:"listitem--employer",props:{employer:null},setup(e){return(l,a)=>{var s,n,p;return e.employer?(r(),y("a",{key:0,href:`/admin/staff-management/pay-runs/${e.employer.id}/${(s=e.employer.currentPayRun)==null?void 0:s.taxYear}`,title:`Go to pay runs of ${e.employer.name}`,class:"grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[t("div",R,[t("div",B,[t("img",{src:e.employer.logoUrl,class:"w-full h-full"},null,8,L)]),t("span",P,o(e.employer.name),1)]),t("div",S,o(e.employer.crn?e.employer.crn:"-"),1),t("div",Y,o(e.employer.employeeCount),1),t("div",j,o((n=e.employer.currentPayRun)==null?void 0:n.taxYear)+" / "+o((p=e.employer.currentPayRun)==null?void 0:p.period),1),t("div",D,o(e.employer.dateSynced),1)],8,E)):i("",!0)}}}),q=m({__name:"list--employers",props:{employers:Object},setup(e){return(l,a)=>(r(!0),y(u,null,x(e.employers,s=>(r(),d(N,{key:s.id,employer:s},null,8,["employer"]))),128))}}),A=t("div",{class:"sm:flex"},[t("div",{class:"sm:flex-auto"},[t("h1",{class:"text-xl font-semibold text-gray-900"}," Employers "),t("p",{class:"mt-2 text-sm text-gray-700"}," Select a company below to begin bulk pay run management. ")])],-1),U={class:"mt-8 flex flex-col w-full"},V={class:"-my-2 overflow-x-auto w-full"},F={class:"inline-block min-w-full py-2 align-middle"},O={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},z=f('<div class="grid grid-cols-6 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"> Company </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> CRN </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Employee count </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Current Pay Run </div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"> Last synced </div></div>',1),G=m({__name:"Employers",setup(e){const{result:l,loading:a}=b(C);return(s,n)=>(r(),y(u,null,[A,t("div",U,[t("div",V,[t("div",F,[t("div",O,[z,c(a)?(r(),d(w,{key:0})):i("",!0),c(l)?(r(),d(q,{key:1,employers:c(l).employers},null,8,["employers"])):i("",!0)])])])])],64))}}),H=async()=>g({setup(){v(k,$)},render:()=>h(G)}).mount("#employer-container");H().then(()=>{console.log()});
//# sourceMappingURL=employers.294f32f8.js.map
