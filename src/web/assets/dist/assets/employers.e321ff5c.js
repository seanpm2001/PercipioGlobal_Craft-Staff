import{g as e}from"./useApolloClient.9f162bd9.js";const o=e`
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
//# sourceMappingURL=employers.e321ff5c.js.map
