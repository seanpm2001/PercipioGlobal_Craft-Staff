var j=Object.defineProperty,A=Object.defineProperties;var L=Object.getOwnPropertyDescriptors;var _=Object.getOwnPropertySymbols;var B=Object.prototype.hasOwnProperty,T=Object.prototype.propertyIsEnumerable;var k=(e,t,o)=>t in e?j(e,t,{enumerable:!0,configurable:!0,writable:!0,value:o}):e[t]=o,D=(e,t)=>{for(var o in t||(t={}))B.call(t,o)&&k(e,o,t[o]);if(_)for(var o of _(t))T.call(t,o)&&k(e,o,t[o]);return e},S=(e,t)=>A(e,L(t));import{g as N,o as n,c as f,a as s,d as C,t as m,n as b,b as p,r as E,e as w,F as z,v as $,u as Q,f as u,h as F,i as H,j as M,y as Y,p as O,D as U}from"./vendor.a01131e1.js";import{_ as V,L as G}from"./listitem--loading.69eca668.js";import{d as J}from"./useApolloClient.4f887e84.js";const K=N`
    query Requests(
        $employerId: [Int], 
        $employeeId: [Int], 
        $type: [String], 
        $status: [String],
        $limit: Int,
        $offset: Int,
    ){
        Requests(
            employerId: $employerId 
            employeeId: $employeeId 
            type: $type 
            status: $status
            limit: $limit,
            offset: $offset,
        ) {
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
        RequestCount(
            employerId: $employerId
            employeeId: $employeeId
            type: $type
            status: $status
        )
    }
`;N`
    mutation CreateRequest($id: Int!, $adminId: Int!, $dateHandled: Date!, $status: String) {
      CreateRequest(
        id: $id,
        administerId: $adminId,
        dateAdministered: $dateHandled,
        status: $status,
      ) {
        id,
        data
      }
    }
`;const W={},X={class:"flex items-center justify-center p-4 border-b border-solid border-gray-200"},Z=s("span",{class:"text-base"}," No results found ",-1),ee=[Z];function te(e,t){return n(),f("div",X,ee)}var se=V(W,[["render",te]]);const oe=["href","title"],re={class:"col-span-2 flex items-center whitespace-nowrap pl-4 pr-3 sm:pl-6 py-4 text-sm text-gray-500"},ie={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},ae={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize"},ne={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},le={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},de={class:"flex items-center whitespace-nowrap px-3 pr-3 py-4 text-sm"},ue=C({props:{request:null},setup(e){return(t,o)=>{var a,l,g,x,h,q,c,i,r,d,y,v;return e.request?(n(),f("a",{key:0,href:`/admin/staff-management/requests/${e.request.id}`,title:`Go to request ${e.request.id}`,class:"grid grid-cols-11 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[s("div",re,m(((g=(l=(a=e.request)==null?void 0:a.employee)==null?void 0:l.personalDetails)==null?void 0:g.firstName)?e.request.employee.personalDetails.firstName+" "+e.request.employee.personalDetails.lastName:"-"),1),s("div",ie,m(((x=e.request)==null?void 0:x.employer)?e.request.employer:"-"),1),s("div",ae,m(((h=e.request)==null?void 0:h.type)?e.request.type.replace("_"," "):"-"),1),s("div",ne,m(((q=e.request)==null?void 0:q.dateCreated)?e.request.dateCreated:"-"),1),s("div",le,m(((c=e.request)==null?void 0:c.administer)?e.request.administer:"-"),1),s("div",de,[s("span",{class:b(["rounded-2xl text-xs font-bold px-3 py-1 mb-0",((i=e.request)==null?void 0:i.status)==="pending"?"bg-yellow-300 text-yellow-900":"",((r=e.request)==null?void 0:r.status)==="approved"?"bg-emerald-300 text-emerald-900":"",((d=e.request)==null?void 0:d.status)==="declined"?"bg-red-300 text-red-900":"",((y=e.request)==null?void 0:y.status)==="canceled"?"bg-gray-300 text-gray-900":""])},m(((v=e.request)==null?void 0:v.status)?e.request.status:"-"),3)])],8,oe)):p("",!0)}}}),ce=C({props:{requests:Object},setup(e){return(t,o)=>(n(!0),f(z,null,E(e.requests,a=>(n(),w(ue,{key:a.id,request:a},null,8,["request"]))),128))}}),me=s("div",{class:"sm:flex"},[s("div",{class:"sm:flex-auto"},[s("h1",{class:"text-xl font-semibold text-gray-900"},"Requests"),s("p",{class:"mt-2 text-sm text-gray-700"},"Select a request to handle the employees request.")])],-1),ge={class:"mt-4 flex justify-end"},pe={class:"relative z-0 inline-flex shadow-sm rounded-md"},fe={class:"mt-8 flex flex-col w-full"},xe={class:"-my-2 overflow-x-auto w-full"},ye={class:"inline-block min-w-full py-2 align-middle"},ve={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},be=F('<div class="grid grid-cols-11 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div></div>',1),he={key:0,class:"animate-spin ml-1 h-3 w-3 text-white mb-0",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},qe=s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),we=s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),$e=[qe,we],Ce=C({setup(e){const t=$({currentPage:0,hitsPerPage:30,total:0}),o=$("all"),a=$({limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage}),{result:l,loading:g,fetchMore:x,onResult:h}=Q(K,a.value);h(i=>{t.value.total=i.data.RequestCount});const q=()=>{t.value.currentPage+=1,x({variables:a.value,updateQuery:(i,{fetchMoreResult:r})=>r?S(D({},i),{Requests:[...i.Requests,...r.Requests],RequestCount:r.RequestCount}):i})},c=i=>{i!=="all"?a.value={limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage,status:i}:a.value={limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage},o.value=i,x({variables:a.value,updateQuery:(r,{fetchMoreResult:d})=>({Requests:d.Requests,RequestCount:d.RequestCount})})};return(i,r)=>{var d,y,v,I,P,R;return n(),f(z,null,[me,s("div",ge,[s("span",pe,[s("button",{onClick:r[0]||(r[0]=()=>c("all")),class:b(["cursor-pointer font-bold relative inline-flex items-center px-4 py-2 rounded-l-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="all"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," All ",2),s("button",{onClick:r[1]||(r[1]=()=>c("pending")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="pending"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Pending ",2),s("button",{onClick:r[2]||(r[2]=()=>c("approved")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="approved"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Approved ",2),s("button",{onClick:r[3]||(r[3]=()=>c("declined")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 rounded-r-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="declined"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Declined ",2)])]),s("div",fe,[s("div",xe,[s("div",ye,[s("div",ve,[be,u(g)?(n(),w(G,{key:0})):p("",!0),!u(g)&&((y=(d=u(l))==null?void 0:d.Requests)==null?void 0:y.length)===0?(n(),w(se,{key:1})):p("",!0),u(l)?(n(),w(ce,{key:2,requests:u(l).Requests},null,8,["requests"])):p("",!0)])])]),((I=(v=u(l))==null?void 0:v.Requests)==null?void 0:I.length)!==t.value.total&&!u(g)?(n(),f("button",{key:0,onClick:q,class:"cursor-pointer mt-6 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"},[s("span",null,"Load more ("+m(t.value.total-((R=(P=u(l))==null?void 0:P.Requests)==null?void 0:R.length))+")",1),i.loadig?(n(),f("svg",he,$e)):p("",!0)])):p("",!0)])],64)}}}),Ie=async()=>{const e=H({setup(){O(U,J)},render:()=>M(Ce)});return e.use(Y()),e.mount("#request-container")};Ie().then(()=>{console.log()});
//# sourceMappingURL=requestsOverview.e51d2099.js.map
