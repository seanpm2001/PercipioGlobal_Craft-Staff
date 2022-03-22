import{g as u,d as i,o as r,c as m,a as e,t as o,r as x,b as d,F as y,u as f,e as c,f as p,h as g,i as h,j as v,p as b,D as _}from"./vendor.c20e18bf.js";import{L as w,d as $}from"./useApolloClient.7975f0d6.js";const C=u`
    query Employers {
        employers {
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
`,k=["href","title"],E={class:"col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6 flex"},L={class:"object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0"},R=["src"],S={style:{"margin-bottom":"0"}},j={class:"whitespace-nowrap px-3 py-4 text-sm text-gray-500"},P={class:"whitespace-nowrap px-3 py-4 text-sm text-gray-500"},Y={class:"whitespace-nowrap px-3 py-4 text-sm text-gray-500"},B={class:"whitespace-nowrap px-3 py-4 text-sm text-gray-500"},D=i({props:{employer:Object},setup(t){return(l,a)=>{var s,n;return r(),m("a",{href:`/admin/staff-management/pay-runs/${t.employer.id}`,title:`Go to pay runs of ${t.employer.name}`,class:"grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[e("div",E,[e("div",L,[e("img",{src:t.employer.logoUrl,class:"w-full h-full"},null,8,R)]),e("span",S,o(t.employer.name),1)]),e("div",j,o(t.employer.crn?t.employer.crn:"-"),1),e("div",P,o(t.employer.employeeCount),1),e("div",Y,o((s=t.employer.currentPayRun)==null?void 0:s.taxYear)+" / "+o((n=t.employer.currentPayRun)==null?void 0:n.period),1),e("div",B,o(t.employer.dateSynced),1)],8,k)}}}),N=i({props:{employers:Object},setup(t){return(l,a)=>(r(!0),m(y,null,x(t.employers,s=>(r(),d(D,{key:s.id,employer:s},null,8,["employer"]))),128))}}),O=e("div",{class:"sm:flex"},[e("div",{class:"sm:flex-auto"},[e("h1",{class:"text-xl font-semibold text-gray-900"},"Employers"),e("p",{class:"mt-2 text-sm text-gray-700"},"Select a company below to begin bulk pay run management.")])],-1),U={class:"mt-8 flex flex-col w-full"},V={class:"-my-2 overflow-x-auto w-full"},q={class:"inline-block min-w-full py-2 align-middle"},A={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},F=g('<div class="grid grid-cols-6 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Company</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">CRN</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Employee count</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Pay Run</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last synced</div></div>',1),G=i({setup(t){const{result:l,loading:a}=f(C);return(s,n)=>(r(),m(y,null,[O,e("div",U,[e("div",V,[e("div",q,[e("div",A,[F,c(a)?(r(),d(w,{key:0})):p("",!0),c(l)?(r(),d(N,{key:1,employers:c(l).employers},null,8,["employers"])):p("",!0)])])])])],64))}}),H=async()=>h({setup(){b(_,$)},render:()=>v(G)}).mount("#employer-container");H().then(()=>{console.log()});
//# sourceMappingURL=employers.f437ec23.js.map
