import { ApolloClient, from, InMemoryCache, HttpLink } from '@apollo/client/core'
import { onError } from '@apollo/client/link/error'
import { setContext } from '@apollo/client/link/context'
import { getToken } from '~/js/composables/useAxiosClient'

// HTTP connection to the API
const httpLink = new HttpLink({
    uri: 'http://localhost:8001/api',
    credentials: 'include'
})

const authLink = setContext(async (_, { headers }) => {
    // get the authentication token from the store, if it's null fetch it through axios
    const token = await getToken()

    //const token = 'SgmAAuUYsFE0_GKOIo7deUOuZWj0yttv'
    return {
        headers: {
            ...headers,
            authorization: `Bearer ${token}`
        }
    }
})

// Handle errors
const errorLink = onError( ({ graphQLErrors, networkError, operation, forward }):any => {} )

export const defaultClient: ApolloClient = new ApolloClient({
    cache: new InMemoryCache(),
    link: from([ errorLink, authLink, httpLink ])
})