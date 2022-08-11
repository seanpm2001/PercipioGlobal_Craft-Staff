var j=Object.defineProperty,A=Object.defineProperties;var L=Object.getOwnPropertyDescriptors;var _=Object.getOwnPropertySymbols;var B=Object.prototype.hasOwnProperty,T=Object.prototype.propertyIsEnumerable;var k=(e,t,o)=>t in e?j(e,t,{enumerable:!0,configurable:!0,writable:!0,value:o}):e[t]=o,S=(e,t)=>{for(var o in t||(t={}))B.call(t,o)&&k(e,o,t[o]);if(_)for(var o of _(t))T.call(t,o)&&k(e,o,t[o]);return e},D=(e,t)=>A(e,L(t));import{g as N,o as n,c as f,a as s,d as C,t as m,n as b,b as g,r as E,e as w,F as z,v as $,u as Q,f as u,h as F,i as M,j as Y,y as O,p as U,D as V}from"./vendor.a01131e1.js";import{_ as G,L as H,d as J}from"./useApolloClient.c6173589.js";const K=N`
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
    mutation CreateRequest($employerId: Int!, $employeeId: Int!, $type: String!, $status: String, $data: String!) {
      CreateRequest(
        employeeId: $employeeId,
        employerId: $employerId,
        type: $type,
        status: $status,
        data: $data
      ) {
        id,
        data
      }
    }
`;const W={},X={class:"flex items-center justify-center p-4 border-b border-solid border-gray-200"},Z=s("span",{class:"text-base"}," No results found ",-1),ee=[Z];function te(e,t){return n(),f("div",X,ee)}var se=G(W,[["render",te]]);const oe=["href","title"],re={class:"col-span-2 flex items-center whitespace-nowrap pl-4 pr-3 sm:pl-6 py-4 text-sm text-gray-500"},ae={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},ie={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize"},ne={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},le={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},de={class:"flex items-center whitespace-nowrap px-3 pr-3 py-4 text-sm"},ue=C({props:{request:null},setup(e){return(t,o)=>{var i,l,p,y,h,q,c,a,r,d,x,v;return e.request?(n(),f("a",{key:0,href:`/admin/staff-management/requests/${e.request.id}`,title:`Go to request ${e.request.id}`,class:"grid grid-cols-11 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[s("div",re,m(((p=(l=(i=e.request)==null?void 0:i.employee)==null?void 0:l.personalDetails)==null?void 0:p.firstName)?e.request.employee.personalDetails.firstName+" "+e.request.employee.personalDetails.lastName:"-"),1),s("div",ae,m(((y=e.request)==null?void 0:y.employer)?e.request.employer:"-"),1),s("div",ie,m(((h=e.request)==null?void 0:h.type)?e.request.type.replace("_"," "):"-"),1),s("div",ne,m(((q=e.request)==null?void 0:q.dateCreated)?e.request.dateCreated:"-"),1),s("div",le,m(((c=e.request)==null?void 0:c.administer)?e.request.administer:"-"),1),s("div",de,[s("span",{class:b(["rounded-2xl text-xs font-bold px-3 py-1 mb-0",((a=e.request)==null?void 0:a.status)==="pending"?"bg-yellow-300 text-yellow-900":"",((r=e.request)==null?void 0:r.status)==="approved"?"bg-emerald-300 text-emerald-900":"",((d=e.request)==null?void 0:d.status)==="declined"?"bg-red-300 text-red-900":"",((x=e.request)==null?void 0:x.status)==="canceled"?"bg-gray-300 text-gray-900":""])},m(((v=e.request)==null?void 0:v.status)?e.request.status:"-"),3)])],8,oe)):g("",!0)}}}),ce=C({props:{requests:Object},setup(e){return(t,o)=>(n(!0),f(z,null,E(e.requests,i=>(n(),w(ue,{key:i.id,request:i},null,8,["request"]))),128))}}),me=s("div",{class:"sm:flex"},[s("div",{class:"sm:flex-auto"},[s("h1",{class:"text-xl font-semibold text-gray-900"},"Requests"),s("p",{class:"mt-2 text-sm text-gray-700"},"Select a request to handle the employees request.")])],-1),pe={class:"mt-4 flex justify-end"},ge={class:"relative z-0 inline-flex shadow-sm rounded-md"},fe={class:"mt-8 flex flex-col w-full"},ye={class:"-my-2 overflow-x-auto w-full"},xe={class:"inline-block min-w-full py-2 align-middle"},ve={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},be=F('<div class="grid grid-cols-11 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div></div>',1),he={key:0,class:"animate-spin ml-1 h-3 w-3 text-white mb-0",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},qe=s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),we=s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),$e=[qe,we],Ce=C({setup(e){const t=$({currentPage:0,hitsPerPage:30,total:0}),o=$("all"),i=$({limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage}),{result:l,loading:p,fetchMore:y,onResult:h}=Q(K,i.value);h(a=>{t.value.total=a.data.RequestCount});const q=()=>{t.value.currentPage+=1,y({variables:i.value,updateQuery:(a,{fetchMoreResult:r})=>r?D(S({},a),{Requests:[...a.Requests,...r.Requests],RequestCount:r.RequestCount}):a})},c=a=>{a!=="all"?i.value={limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage,status:a}:i.value={limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage},o.value=a,y({variables:i.value,updateQuery:(r,{fetchMoreResult:d})=>({Requests:d.Requests,RequestCount:d.RequestCount})})};return(a,r)=>{var d,x,v,I,P,R;return n(),f(z,null,[me,s("div",pe,[s("span",ge,[s("button",{onClick:r[0]||(r[0]=()=>c("all")),class:b(["cursor-pointer font-bold relative inline-flex items-center px-4 py-2 rounded-l-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="all"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," All ",2),s("button",{onClick:r[1]||(r[1]=()=>c("pending")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="pending"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Pending ",2),s("button",{onClick:r[2]||(r[2]=()=>c("approved")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="approved"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Approved ",2),s("button",{onClick:r[3]||(r[3]=()=>c("declined")),class:b(["cursor-pointer font-bold -ml-px relative inline-flex items-center px-4 py-2 rounded-r-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",o.value==="declined"?"bg-indigo-600 text-white":"bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white"])}," Declined ",2)])]),s("div",fe,[s("div",ye,[s("div",xe,[s("div",ve,[be,u(p)?(n(),w(H,{key:0})):g("",!0),!u(p)&&((x=(d=u(l))==null?void 0:d.Requests)==null?void 0:x.length)===0?(n(),w(se,{key:1})):g("",!0),u(l)?(n(),w(ce,{key:2,requests:u(l).Requests},null,8,["requests"])):g("",!0)])])]),((I=(v=u(l))==null?void 0:v.Requests)==null?void 0:I.length)!==t.value.total&&!u(p)?(n(),f("button",{key:0,onClick:q,class:"cursor-pointer mt-6 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"},[s("span",null,"Load more ("+m(t.value.total-((R=(P=u(l))==null?void 0:P.Requests)==null?void 0:R.length))+")",1),a.loadig?(n(),f("svg",he,$e)):g("",!0)])):g("",!0)])],64)}}}),Ie=async()=>{const e=M({setup(){U(V,J)},render:()=>Y(Ce)});return e.use(O()),e.mount("#request-container")};Ie().then(()=>{console.log()});
//# sourceMappingURL=requestsOverview.62867b7c.js.map
