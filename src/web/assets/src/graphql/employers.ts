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
            dateSynced:dateUpdated @formatDateTime(format:"Y-m-d H:i")
            currentPayRun{
                taxYear
                period
            }
        }
    }
`