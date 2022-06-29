import{g as m,a as p}from"./useApolloClient.4d3839c7.js";import{d as f,r as d,e as n,f as i,u,t as y,L as _,i as h,H as x,m as S}from"./vue.esm-bundler.5b1e7a16.js";var D=!1;const B=m`
    query Payruns($employerId: [ID], $taxYear: [String]) {
        payruns(employerId: $employerId, taxYear: $taxYear, orderBy: "startDate desc") {
            id,
            employerId
            taxYear
            period
            employeeCount
            startDate @formatDateTime(format:"jS M, Y")
            endDate @formatDateTime(format:"jS M, Y")
            paymentDate @formatDateTime(format:"jS M, Y")
            dateUpdated @formatDateTime(format:"jS M, Y")
            dateSynced:dateUpdated @formatDateTime(format:"Y-m-d H:i")
            employer
            state
            totals{
                totalCost
            }
        }
    }
`,M=m`
    query Payrun($id: [QueryArgument]) {
        payrun(id: $id) {
            id,
            paymentDate @formatDateTime(format:"j M, Y")
            dateSynced:dateUpdated @formatDateTime(format:"Y-m-d H:i")
            employerId
            taxYear
            period
            totals {
                totalCost
                gross
                tax
                employerNi
                employeeNi
            }
        }
    }
`,U=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),Y={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},g=_(" Last Synced: "),P={key:0,class:"flex items-center pl-1"},$=h("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),b=[$],j={key:1,class:"pl-1"},q=f({__name:"status--synced",props:{date:String},setup(e){const r=p();return d(null),(s,o)=>(n(),i("span",Y,[g,u(r).queue!=0?(n(),i("span",P,b)):(n(),i("span",j,y(e.date),1))]))}});/*!
  * pinia v2.0.14
  * (c) 2022 Eduardo San Martin Morote
  * @license MIT
  */const I=e=>e,T=Symbol();var c;(function(e){e.direct="direct",e.patchObject="patch object",e.patchFunction="patch function"})(c||(c={}));function v(){const e=x(!0),r=e.run(()=>d({}));let s=[],o=[];const a=S({install(t){I(a),a._a=t,t.provide(T,a),t.config.globalProperties.$pinia=a,o.forEach(l=>s.push(l)),o=[]},use(t){return!this._a&&!D?o.push(t):s.push(t),this},_p:s,_a:null,_e:e,_s:new Map,state:r});return a}export{B as P,q as _,M as a,v as c,U as f};
//# sourceMappingURL=pinia.77013b47.js.map
