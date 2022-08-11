import{g as D,d as m,o as r,c as u,a as t,t as a,n as I,b as n,r as S,e as c,F as $,u as C,f as i,h as k,i as N,j as R,y as j,p as B,D as T}from"./vendor.a01131e1.js";import{L as A,d as E}from"./useApolloClient.c6173589.js";const L=D`
    query Requests($employerId: [Int], $employeeId: [Int], $type: [String], $status: [String]){
        Requests(employerId: $employerId, employeeId: $employeeId, type: $type, status: $status) {
            id
            data
            administerId
            dateAdministered @formatDateTime(format:"jS M, Y")
            employerId
            employeeId
            dateUpdated @formatDateTime(format:"jS M, Y")
            dateCreated @formatDateTime(format:"jS M, Y")
            status
            employer
            type
            employee {
                personalDetails {
                    firstName
                    lastName
                }
            }
        }
    }
`,M=["href","title"],V={class:"col-span-2 flex items-center whitespace-nowrap pl-4 pr-3 sm:pl-6 py-4 text-sm text-gray-500"},Y={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},_={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize"},z={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},F={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},O={class:"flex items-center whitespace-nowrap px-3 pr-3 py-4 text-sm"},Q=m({props:{request:null},setup(e){return(l,o)=>{var s,d,p,y,x,f,g,q,h,v,b,w;return e.request?(r(),u("a",{key:0,href:`/admin/staff-management/requests/${e.request.id}`,title:`Go to request ${e.request.id}`,class:"grid grid-cols-11 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[t("div",V,a(((p=(d=(s=e.request)==null?void 0:s.employee)==null?void 0:d.personalDetails)==null?void 0:p.firstName)?e.request.employee.personalDetails.firstName+" "+e.request.employee.personalDetails.lastName:"-"),1),t("div",Y,a(((y=e.request)==null?void 0:y.employer)?e.request.employer:"-"),1),t("div",_,a(((x=e.request)==null?void 0:x.type)?e.request.type.replace("_"," "):"-"),1),t("div",z,a(((f=e.request)==null?void 0:f.dateCreated)?e.request.dateCreated:"-"),1),t("div",F,a(((g=e.request)==null?void 0:g.administer)?e.request.administer:"-"),1),t("div",O,[t("span",{class:I(["rounded-2xl text-xs font-bold px-3 py-1 mb-0",((q=e.request)==null?void 0:q.status)==="pending"?"bg-yellow-300 text-yellow-900":"",((h=e.request)==null?void 0:h.status)==="approved"?"bg-emerald-300 text-emerald-900":"",((v=e.request)==null?void 0:v.status)==="declined"?"bg-red-300 text-red-900":"",((b=e.request)==null?void 0:b.status)==="canceled"?"bg-gray-300 text-gray-900":""])},a(((w=e.request)==null?void 0:w.status)?e.request.status:"-"),3)])],8,M)):n("",!0)}}}),U=m({props:{requests:Object},setup(e){return(l,o)=>(r(!0),u($,null,S(e.requests,s=>(r(),c(Q,{key:s.id,request:s},null,8,["request"]))),128))}}),G=t("div",{class:"sm:flex"},[t("div",{class:"sm:flex-auto"},[t("h1",{class:"text-xl font-semibold text-gray-900"},"Requests"),t("p",{class:"mt-2 text-sm text-gray-700"},"Select a request to handle the employees request.")])],-1),P={class:"mt-8 flex flex-col w-full"},H={class:"-my-2 overflow-x-auto w-full"},J={class:"inline-block min-w-full py-2 align-middle"},K={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},W=k('<div class="grid grid-cols-11 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div></div>',1),X=m({setup(e){const{result:l,loading:o}=C(L);return(s,d)=>(r(),u($,null,[G,t("div",P,[t("div",H,[t("div",J,[t("div",K,[W,i(o)?(r(),c(A,{key:0})):n("",!0),i(l)?(r(),c(U,{key:1,requests:i(l).Requests},null,8,["requests"])):n("",!0)])])])])],64))}}),Z=async()=>{const e=N({setup(){B(T,E)},render:()=>R(X)});return e.use(j()),e.mount("#request-container")};Z().then(()=>{console.log()});
//# sourceMappingURL=requestsOverview.0bc52c78.js.map
