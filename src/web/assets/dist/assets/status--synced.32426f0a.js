import{g as o,b as s}from"./useApolloClient.d09a6589.js";import{d as n,a as m,o as t,b as a,u as d,t as i,j as p,e as l}from"./vue.esm-bundler.5316b77a.js";const P=o`
    query PayRuns($employerId: [ID], $taxYear: [String]) {
        payruns: PayRuns(employerId: $employerId, taxYear: $taxYear, orderBy: "startDate desc") {
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
`,T=o`
    query PayRun($id: [QueryArgument]) {
        payrun: PayRun(id: $id) {
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
`,h=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),y={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},c=p(" Last Synced: "),f={key:0,class:"flex items-center pl-1"},u=l("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),_=[u],x={key:1,class:"pl-1"},R=n({__name:"status--synced",props:{date:String},setup(e){const r=s();return m(null),(D,Y)=>(t(),a("span",y,[c,d(r).queue!=0?(t(),a("span",f,_)):(t(),a("span",x,i(e.date),1))]))}});export{T as P,R as _,P as a,h as f};
//# sourceMappingURL=status--synced.32426f0a.js.map
