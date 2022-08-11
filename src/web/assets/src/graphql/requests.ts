import { gql } from 'graphql-tag'

export const REQUESTS = gql`
    query Requests($employerId: [Int], $employeeId: [Int], $type: [String], $status: [String]){
        Requests(employerId: $employerId, employeeId: $employeeId, type: $type, status: $status) {
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