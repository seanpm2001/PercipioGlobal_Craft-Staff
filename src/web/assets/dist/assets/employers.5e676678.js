import{g as u,d as m,o as r,c as y,a as e,t as o,b as d,r as x,e as i,F as p,u as f,f as c,h as g,i as h,p as v,j as b,D as _}from"./vendor.c43b4d0a.js";import{L as w,d as $}from"./useApolloClient.a9f4758e.js";const k=u`
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
`,C=["href","title"],E={class:"flex items-center col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6"},L={class:"object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0"},R=["src"],S={style:{"margin-bottom":"0"}},B={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},P={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},Y={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},j={class:"flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},D=m({props:{employer:null},setup(t){return(l,a)=>{var s,n;return t.employer?(r(),y("a",{key:0,href:`/admin/staff-management/pay-runs/${t.employer.id}`,title:`Go to pay runs of ${t.employer.name}`,class:"grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[e("div",E,[e("div",L,[e("img",{src:t.employer.logoUrl,class:"w-full h-full"},null,8,R)]),e("span",S,o(t.employer.name),1)]),e("div",B,o(t.employer.crn?t.employer.crn:"-"),1),e("div",P,o(t.employer.employeeCount),1),e("div",Y,o((s=t.employer.currentPayRun)==null?void 0:s.taxYear)+" / "+o((n=t.employer.currentPayRun)==null?void 0:n.period),1),e("div",j,o(t.employer.dateSynced),1)],8,C)):d("",!0)}}}),N=m({props:{employers:Object},setup(t){return(l,a)=>(r(!0),y(p,null,x(t.employers,s=>(r(),i(D,{key:s.id,employer:s},null,8,["employer"]))),128))}}),U=e("div",{class:"sm:flex"},[e("div",{class:"sm:flex-auto"},[e("h1",{class:"text-xl font-semibold text-gray-900"},"Employers"),e("p",{class:"mt-2 text-sm text-gray-700"},"Select a company below to begin bulk pay run management.")])],-1),V={class:"mt-8 flex flex-col w-full"},q={class:"-my-2 overflow-x-auto w-full"},A={class:"inline-block min-w-full py-2 align-middle"},F={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},O=g('<div class="grid grid-cols-6 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Company</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">CRN</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Employee count</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Pay Run</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last synced</div></div>',1),G=m({setup(t){const{result:l,loading:a}=f(k);return(s,n)=>(r(),y(p,null,[U,e("div",V,[e("div",q,[e("div",A,[e("div",F,[O,c(a)?(r(),i(w,{key:0})):d("",!0),c(l)?(r(),i(N,{key:1,employers:c(l).employers},null,8,["employers"])):d("",!0)])])])])],64))}}),H=async()=>h({setup(){v(_,$)},render:()=>b(G)}).mount("#employer-container");H().then(()=>{console.log()});
//# sourceMappingURL=employers.5e676678.js.map
