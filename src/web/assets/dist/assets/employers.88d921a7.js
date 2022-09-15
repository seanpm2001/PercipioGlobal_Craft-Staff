import{g as e}from"./index.734efbd9.js";const a=e`
    query Employers {
        Employers(orderBy: "name desc") {
            id
            crn
            name
            employeeCount
            currentYear
            logoUrl
            dateSynced:dateUpdated @formatDateTime(format:"Y-m-d H:i")
            currentPayRun{
                taxYear
                period
            }
        }
    }
`;export{a as E};
//# sourceMappingURL=employers.88d921a7.js.map
