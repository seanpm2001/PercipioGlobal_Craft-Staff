var k=Object.defineProperty,D=Object.defineProperties;var _=Object.getOwnPropertyDescriptors;var $=Object.getOwnPropertySymbols;var N=Object.prototype.hasOwnProperty,j=Object.prototype.propertyIsEnumerable;var I=(e,t,a)=>t in e?k(e,t,{enumerable:!0,configurable:!0,writable:!0,value:a}):e[t]=a,C=(e,t)=>{for(var a in t||(t={}))N.call(t,a)&&I(e,a,t[a]);if($)for(var a of $(t))j.call(t,a)&&I(e,a,t[a]);return e},P=(e,t)=>D(e,_(t));import{g as R,d as v,o,c as f,a as s,t as i,n as B,b as y,r as L,e as q,F as S,v as A,u as M,f as p,h as T,i as z,j as E,y as V,p as Q,D as Y}from"./vendor.a01131e1.js";import{L as F,d as O}from"./useApolloClient.c6173589.js";const U=R`
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
        RequestCount
    }
`;R`
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
`;const G=["href","title"],H={class:"col-span-2 flex items-center whitespace-nowrap pl-4 pr-3 sm:pl-6 py-4 text-sm text-gray-500"},J={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},K={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500 capitalize"},W={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},X={class:"col-span-2 flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500"},Z={class:"flex items-center whitespace-nowrap px-3 pr-3 py-4 text-sm"},ee=v({props:{request:null},setup(e){return(t,a)=>{var l,x,g,h,r,n,d,u,c,m,b,w;return e.request?(o(),f("a",{key:0,href:`/admin/staff-management/requests/${e.request.id}`,title:`Go to request ${e.request.id}`,class:"grid grid-cols-11 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"},[s("div",H,i(((g=(x=(l=e.request)==null?void 0:l.employee)==null?void 0:x.personalDetails)==null?void 0:g.firstName)?e.request.employee.personalDetails.firstName+" "+e.request.employee.personalDetails.lastName:"-"),1),s("div",J,i(((h=e.request)==null?void 0:h.employer)?e.request.employer:"-"),1),s("div",K,i(((r=e.request)==null?void 0:r.type)?e.request.type.replace("_"," "):"-"),1),s("div",W,i(((n=e.request)==null?void 0:n.dateCreated)?e.request.dateCreated:"-"),1),s("div",X,i(((d=e.request)==null?void 0:d.administer)?e.request.administer:"-"),1),s("div",Z,[s("span",{class:B(["rounded-2xl text-xs font-bold px-3 py-1 mb-0",((u=e.request)==null?void 0:u.status)==="pending"?"bg-yellow-300 text-yellow-900":"",((c=e.request)==null?void 0:c.status)==="approved"?"bg-emerald-300 text-emerald-900":"",((m=e.request)==null?void 0:m.status)==="declined"?"bg-red-300 text-red-900":"",((b=e.request)==null?void 0:b.status)==="canceled"?"bg-gray-300 text-gray-900":""])},i(((w=e.request)==null?void 0:w.status)?e.request.status:"-"),3)])],8,G)):y("",!0)}}}),te=v({props:{requests:Object},setup(e){return(t,a)=>(o(!0),f(S,null,L(e.requests,l=>(o(),q(ee,{key:l.id,request:l},null,8,["request"]))),128))}}),se=s("div",{class:"sm:flex"},[s("div",{class:"sm:flex-auto"},[s("h1",{class:"text-xl font-semibold text-gray-900"},"Requests"),s("p",{class:"mt-2 text-sm text-gray-700"},"Select a request to handle the employees request.")])],-1),ae={class:"mt-8 flex flex-col w-full"},re={class:"-my-2 overflow-x-auto w-full"},oe={class:"inline-block min-w-full py-2 align-middle"},le={class:"overflow-hidden shadow border border-solid border-gray-300 md:rounded-lg"},ie=T('<div class="grid grid-cols-11 border-b border-solid border-gray-300"><div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div><div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div><div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div></div>',1),ne={key:0,class:"animate-spin ml-1 h-3 w-3 text-white mb-0",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},de=s("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"},null,-1),ue=s("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},null,-1),ce=[de,ue],me=v({setup(e){const t=A({currentPage:0,hitsPerPage:5,total:0}),{result:a,loading:l,fetchMore:x,onResult:g}=M(U,{limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage});g(r=>{t.value.total=r.data.RequestCount});const h=()=>{t.value.currentPage+=1,x({variables:{limit:t.value.hitsPerPage,offset:t.value.currentPage*t.value.hitsPerPage},updateQuery:(r,{fetchMoreResult:n})=>n?P(C({},r),{Requests:[...r.Requests,...n.Requests]}):r})};return(r,n)=>{var d,u,c,m;return o(),f(S,null,[se,s("div",ae,[s("div",re,[s("div",oe,[s("div",le,[ie,p(l)?(o(),q(F,{key:0})):y("",!0),p(a)?(o(),q(te,{key:1,requests:p(a).Requests},null,8,["requests"])):y("",!0)])])]),((u=(d=p(a))==null?void 0:d.Requests)==null?void 0:u.length)!==t.value.total?(o(),f("button",{key:0,onClick:h,class:"cursor-pointer mt-6 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"},[s("span",null,"Load more ("+i(t.value.total-((m=(c=p(a))==null?void 0:c.Requests)==null?void 0:m.length))+")",1),r.loadig?(o(),f("svg",ne,ce)):y("",!0)])):y("",!0)])],64)}}}),pe=async()=>{const e=z({setup(){Q(Y,O)},render:()=>E(me)});return e.use(V()),e.mount("#request-container")};pe().then(()=>{console.log()});
//# sourceMappingURL=requestsOverview.bc0e5af7.js.map
