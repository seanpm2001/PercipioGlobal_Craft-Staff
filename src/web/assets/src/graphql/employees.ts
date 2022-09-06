import { gql } from 'graphql-tag'

export const EMPLOYEES = gql`
    query Employees($employerId: [Int]) {
          Employees(employerId: $employerId) {
                id
                personalDetails {
                      firstName
                      lastName
                }
          }
    }

`