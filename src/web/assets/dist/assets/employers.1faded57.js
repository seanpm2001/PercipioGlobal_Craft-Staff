import{g as e}from"./useApolloClient.63c21703.js";const o=e`
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
//# sourceMappingURL=employers.1faded57.js.map
