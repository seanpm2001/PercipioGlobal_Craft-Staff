import { ApolloClient, from, InMemoryCache, HttpLink } from '@apollo/client/core'
import { onError } from '@apollo/client/link/error'

// HTTP connection to the API
const httpLink = new HttpLink({
    uri: '',
    credentials: 'include'
})

// Handle errors
const errorLink = onError( ({ graphQLErrors, networkError, operation, forward }):any => {} )

export const defaultClient: ApolloClient = new ApolloClient({
    cache: new InMemoryCache(),
    link: from([ errorLink, httpLink ])
})