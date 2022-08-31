import{g as e}from"./useApolloClient.dd8eacf8.js";const a=e`
    query Requests(
        $employerId: [Int], 
        $employeeId: [Int], 
        $type: [String], 
        $status: [String],
        $limit: Int,
        $offset: Int,
    ){
        Requests(
            employerId: $employerId 
            employeeId: $employeeId 
            type: $type 
            status: $status
            limit: $limit,
            offset: $offset,
        ) {
            id
            data
            administerId
            dateAdministered @formatDateTime(format:"jS M, Y")
            employerId
            employeeId
            dateUpdated @formatDateTime(format:"jS M, Y")
            dateCreated @formatDateTime(format:"jS M, Y")
            status
            employer
            type
            employee {
                personalDetails {
                    firstName
                    lastName
                }
            }
            admin
        }
        RequestCount(
            employerId: $employerId
            employeeId: $employeeId
            type: $type
            status: $status
        )
    }
`;e`
    mutation CreateRequest($id: Int!, $adminId: Int!, $dateHandled: Date!, $status: String) {
        CreateRequest(
            id: $id,
            administerId: $adminId,
            dateAdministered: $dateHandled,
            status: $status,
        ) {
            id,
            data
        }
    }
`;const d=e`
    mutation UpdateRequest($id: Int!, $adminId: Int!, $status: String, $note: String) {
        UpdateRequest(
            id: $id
            administerId: $adminId
            status: $status
            note: $note
        ) {
            id
            data
            administerId
            dateAdministered @formatDateTime(format:"jS M, Y")
            employerId
            employeeId
            dateUpdated @formatDateTime(format:"jS M, Y")
            dateCreated @formatDateTime(format:"jS M, Y")
            status
            employer
            type
            employee {
                personalDetails {
                    firstName
                    lastName
                }
            }
            admin
        }
    }
`;export{a as R,d as U};
//# sourceMappingURL=requests.aaf6746f.js.map
