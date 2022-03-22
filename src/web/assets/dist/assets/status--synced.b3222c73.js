import{g as r,d as n,y as m,z as d,B as i,o as t,c as a,e as l,t as p,C as c,a as y}from"./vendor.c20e18bf.js";import{u,g as f}from"./useApolloClient.7975f0d6.js";const N=r`
    query Payruns($employerId: [ID]) {
        payruns(employerId: $employerId) {
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
`,P=r`
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
`,U=e=>e&&e.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,"$&,"),_={class:"mt-4 md:mt-0 text-xs inline-flex mr-2 flex-grow",style:{"margin-bottom":"0"}},g=c(" Last Synced: "),D={key:0,class:"flex items-center pl-1"},x=y("span",{style:{"margin-bottom":"0"}},"Queue is running to sync",-1),S=[x],Y={key:1,class:"pl-1"},j=n({props:{date:String},setup(e){const s=u(),o=m(null);return d(()=>{o.value=setInterval(()=>{f()},5e3)}),i(()=>{clearInterval(o.value)}),(I,T)=>(t(),a("span",_,[g,l(s).queue!=0?(t(),a("span",D,S)):(t(),a("span",Y,p(e.date),1))]))}});export{N as P,j as _,P as a,U as f};
//# sourceMappingURL=status--synced.b3222c73.js.map
