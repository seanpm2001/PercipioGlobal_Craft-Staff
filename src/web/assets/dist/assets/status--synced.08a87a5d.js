import{g as o,d as s,x as n,o as t,c as a,f as m,t as d,y as i,a as p}from"./vendor.ace9cd94.js";import{u as l}from"./useApolloClient.3973916d.js";const P=o`
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
    query Payrun($id: [QueryArgument]) {
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
`,h=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),y={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},c=i(" Last Synced: "),f={key:0,class:"flex items-center pl-1"},u=p("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),_=[u],x={key:1,class:"pl-1"},$=s({props:{date:String},setup(e){const r=l();return n(null),(D,Y)=>(t(),a("span",y,[c,m(r).queue!=0?(t(),a("span",f,_)):(t(),a("span",x,d(e.date),1))]))}});export{P,$ as _,T as a,h as f};
//# sourceMappingURL=status--synced.08a87a5d.js.map
