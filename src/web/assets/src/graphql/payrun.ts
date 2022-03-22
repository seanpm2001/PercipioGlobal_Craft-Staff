import { gql } from 'graphql-tag'

export const PAYRUNS = gql`
    query Payruns($employerId: [ID]) {
        payruns(employerId: $employerId) {
            id,
            employerId
            taxYear
            period
            employeeCount
            startDate @formatDateTime(format:"Y-m-d")
            endDate @formatDateTime(format:"Y-m-d")
            paymentDate @formatDateTime(format:"Y-m-d")
            dateUpdated @formatDateTime(format:"Y-m-d")
            employer
            state
            totals{
                totalCost
            }
        }
    }
`

export const PAYRUN = gql`
    query Payrun($id: [QueryArgument]) {
        payrun(id: $id) {
            id,
            paymentDate @formatDateTime(format:"Y-m-d")
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