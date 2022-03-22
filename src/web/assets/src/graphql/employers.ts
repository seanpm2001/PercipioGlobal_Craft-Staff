import { gql } from 'graphql-tag'

export const EMPLOYERS = gql`
    query Employers {
        employers {
            id
            crn
            name
            employeeCount
            currentYear
            logoUrl
            dateUpdated
            currentPayRun{
                taxYear
                period
            }
        }
    }
`