import{g as e}from"./useApolloClient.ea2bda77.js";const o=e`
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
//# sourceMappingURL=employers.8cae57ff.js.map
