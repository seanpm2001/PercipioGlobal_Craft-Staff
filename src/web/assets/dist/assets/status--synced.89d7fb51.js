import{g as r,a as s}from"./useApolloClient.52323202.js";import{d as n,r as m,f as t,i as a,u as d,v as i,K as p,j as l}from"./runtime-dom.esm-bundler.723b1be6.js";const T=r`
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
`,h=r`
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
`,$=e=>e&&parseFloat(e).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),c={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},y=p(" Last Synced: "),f={key:0,class:"flex items-center pl-1"},u=l("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),_=[u],x={key:1,class:"pl-1"},j=n({__name:"status--synced",props:{date:String},setup(e){const o=s();return m(null),(D,Y)=>(t(),a("span",c,[y,d(o).queue!=0?(t(),a("span",f,_)):(t(),a("span",x,i(e.date),1))]))}});export{T as P,j as _,h as a,$ as f};
//# sourceMappingURL=status--synced.89d7fb51.js.map
