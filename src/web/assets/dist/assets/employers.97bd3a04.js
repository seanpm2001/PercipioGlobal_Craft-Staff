import{g as e}from"./useApolloClient.40cb325f.js";const a=e`
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
//# sourceMappingURL=employers.97bd3a04.js.map
