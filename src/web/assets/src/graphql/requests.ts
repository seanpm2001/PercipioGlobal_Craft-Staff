import { gql } from 'graphql-tag'

export const REQUESTS = gql`
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
        }
        RequestCount(
            employerId: $employerId
            employeeId: $employeeId
            type: $type
            status: $status
        )
    }
`

export const WRITE_REQUEST = gql`
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
`;