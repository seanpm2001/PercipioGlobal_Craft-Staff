import { ApolloClient, from, InMemoryCache, HttpLink } from '@apollo/client/core'
import { onError } from '@apollo/client/link/error'
import { setContext } from '@apollo/client/link/context'
import { getToken } from '~/js/composables/useAxiosClient'



// HTTP connection to the API
const httpLink = new HttpLink({
    uri: '',
    credentials: 'include'
})

const authLink = setContext((_, { headers }) => {
    // get the authentication token from the store, if it's null fetch it through axios
    return {
        headers: {
            ...headers,
            authorization: token ? `Authorization: Bearer ${token}` : null
        }
    }
})

// Handle errors
const errorLink = onError( ({ graphQLErrors, networkError, operation, forward }):any => {} )

export const defaultClient: ApolloClient = new ApolloClient({
    cache: new InMemoryCache(),
    link: from([ errorLink, httpLink ])
})