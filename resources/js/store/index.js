import Axios from "axios"

export default {
    state: {
        users: [],
        userMessages: []
    },
    getters: {
        getUsers(state) {
            return state.users
        },
        getUserMessages(state) {
            return state.userMessages
        }
    },
    actions: {
        getUsers(context) {
            Axios.get('/user-list')
                .then(response => context.commit('usersList', response.data))
        },
        getUserMessages(context, payload) {
            Axios.get('/user-messages/' + payload).then(response => context.commit('userMessages', response.data))
        }
    },
    mutations: {
        usersList(state, payload) {
            return state.users = payload
        },
        userMessages(state, payload) {
            return state.userMessages = payload
        }

    }
}