var f=Object.defineProperty,m=Object.defineProperties;var y=Object.getOwnPropertyDescriptors;var r=Object.getOwnPropertySymbols;var _=Object.prototype.hasOwnProperty,k=Object.prototype.propertyIsEnumerable;var i=(e,t,a)=>t in e?f(e,t,{enumerable:!0,configurable:!0,writable:!0,value:a}):e[t]=a,u=(e,t)=>{for(var a in t||(t={}))_.call(t,a)&&i(e,a,t[a]);if(r)for(var a of r(t))k.call(t,a)&&i(e,a,t[a]);return e},d=(e,t)=>m(e,y(t));import{o as w,c as p,a as c,k as v,l as n,H as x,s as $,A as L,I as C,m as b}from"./vendor.6feb70cb.js";var B=(e,t)=>{const a=e.__vccOpts||e;for(const[s,g]of t)a[s]=g;return a};const P={},q={class:"flex items-center justify-center p-4 border-b border-solid border-gray-200"},F=c("svg",{class:"animate-spin mr-3 h-5 w-5 text-indigo-900",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},[c("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"}),c("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"})],-1),R=c("span",{class:"text-base"}," Loading data ... ",-1),z=[F,R];function A(e,t){return w(),p("div",q,z)}var O=B(P,[["render",A]]);const l=v("payrun",{state:()=>({queue:0,fetching:!1,logs:[]})});var h;const o=(h=window.api.cpUrl)!=null?h:"https://localhost:8003",S=e=>{const t=l();t.loadingFetched=!0,n({method:"get",url:`${o}/staff-management/pay-runs/fetch-pay-runs/${e}`}).then(()=>{t.loadingFetched=!1})},T=e=>{const t=l();t.loadingFetched=!0,n({method:"get",url:`${o}/staff-management/pay-runs/fetch-pay-run/${e}`}).then(()=>{t.loadingFetched=!1})},V=()=>{const e=l();n({method:"get",url:`${o}/staff-management/pay-runs/queue`}).then(t=>{var a;e.queue=((a=t==null?void 0:t.data)==null?void 0:a.total)?t.data.total:0})},j=e=>{const t=l();n({method:"get",url:`${o}/staff-management/pay-runs/get-logs/${e}`}).then(a=>{var s;t.logs=((s=a==null?void 0:a.data)==null?void 0:s.logs)?a.data.logs:[]})},H=async()=>{const e={value:null};return await n({method:"get",url:`${o}/staff-management/settings/get-gql-token`}).then(t=>{var a;e.value=((a=t==null?void 0:t.data)==null?void 0:a.token)?t.data.token:null}),e.value||null},I=new x({uri:"http://localhost:8001/api",credentials:"include"}),N=$(async(e,{headers:t})=>{const a=await H();return{headers:d(u({},t),{authorization:`Bearer ${a}`})}}),D=new L({cache:new C,link:b([N,I])});export{O as L,B as _,j as a,T as b,D as d,S as f,V as g,l as u};
//# sourceMappingURL=useApolloClient.3c1b9148.js.map