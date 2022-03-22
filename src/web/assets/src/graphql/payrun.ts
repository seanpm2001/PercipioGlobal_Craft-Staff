import { gql } from 'graphql-tag'

export const PAYRUNS = gql`
    query Payruns($employerId: ID!) {
        payrun(employerId: $employerId) {
            id,
            paymentDate
            totals {
                totalCost
                gross
                tax
                grossForNi
            }
        }
    }
`

export const PAYRUN = gql`
    query Payrun($id: [QueryArgument]) {
        payrun(id: $id) {
            id,
            paymentDate
            employerId
            taxYear
            period
            totals {
                totalCost
                gross
                tax
                grossForNi
            }
        }
    }
`