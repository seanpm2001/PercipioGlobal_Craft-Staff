import{g as o}from"./index.638a625a.js";import{d as s,a as n,o as t,b as a,u as m,t as d,j as i,e as p}from"./vue.esm-bundler.11cac8fc.js";import{g as l}from"./useApolloClient.2a7a9d07.js";const T=o`
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
`,h=o`
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
`,R=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),y={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},c=i(" Last Synced: "),f={key:0,class:"flex items-center pl-1"},u=p("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),_=[u],x={key:1,class:"pl-1"},$=s({__name:"status--synced",props:{date:String},setup(e){const r=l();return n(null),(D,Y)=>(t(),a("span",y,[c,m(r).queue!=0?(t(),a("span",f,_)):(t(),a("span",x,d(e.date),1))]))}});export{h as P,$ as _,T as a,R as f};
//# sourceMappingURL=status--synced.2cdbd93e.js.map
