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
    mutation CreateRequest($employerId: Int!, $employeeId: Int!, $type: String!, $status: String, $data: String!) {
      CreateRequest(
        employeeId: $employeeId,
        employerId: $employerId,
        type: $type,
        status: $status,
        data: $data
      ) {
        id,
        data
      }
    }
`;