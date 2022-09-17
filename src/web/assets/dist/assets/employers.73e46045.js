import{g as e}from"./index.638a625a.js";const a=e`
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
//# sourceMappingURL=employers.73e46045.js.map
