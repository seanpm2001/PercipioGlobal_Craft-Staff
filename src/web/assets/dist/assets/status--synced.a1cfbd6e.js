import{g as o,d as s,x as m,o as t,c as a,f as n,t as d,y as i,a as p}from"./vendor.ace9cd94.js";import{u as l}from"./useApolloClient.3973916d.js";const T=o`
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
`,h=o`
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
`,$=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),c={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},y=i(" Last Synced: "),f={key:0,class:"flex items-center pl-1"},u=p("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),_=[u],x={key:1,class:"pl-1"},I=s({props:{date:String},setup(e){const r=l();return m(null),(D,Y)=>(t(),a("span",c,[y,n(r).queue!=0?(t(),a("span",f,_)):(t(),a("span",x,d(e.date),1))]))}});export{T as P,I as _,h as a,$ as f};
//# sourceMappingURL=status--synced.a1cfbd6e.js.map
