import{g as e}from"./useApolloClient.dd8eacf8.js";const o=e`
    query Employers {
        employers: Employers(orderBy: "name desc") {
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
`;export{o as E};
//# sourceMappingURL=employers.05882340.js.map
