<template>
    <div id="app">
        <div class="header">
            <div>
                <a href="#" @click="tab = 'board'">Home</a>
            </div>

            <div class="right" v-if="user">
                <a href="#">{{ user.first_name }} {{ user.last_name }}</a>
                <a href="#" @click.prevent="doLogout">Logout</a>
            </div>

        </div>
        <div v-if="!user">

            <div class="form">
                <header>Login</header>
                <form action="" @submit.prevent="doLogin">
                    <input type="text" placeholder="Username" v-model="login.username">
                    <input type="password" placeholder="Password" v-model="login.password">
                    <input type="submit" value="Login">
                </form>
            </div>
            <div class="form">
                <header>Register</header>
                <form action="">
                    <input type="text" placeholder="First Name">
                    <input type="text" placeholder="Last Name">
                    <input type="text" placeholder="Username">
                    <input type="password" placeholder="Password">
                    <input type="password" placeholder="Confirm Password">
                    <input type="submit" value="Register">
                </form>
            </div>

        </div>

        <div v-else>

            <div class="board-container" v-if="tab === 'board'">
                <div class="board-wrapper" v-for="board in boards">
                    <form class="board" @submit.prevent="saveBoard">
                        <span v-if="action === 'edit board' && new_board.id === board.id">
                            <input type="text" v-model="new_board.name" autofocus>
                        </span>
                        <span>{{ board.name }}</span>
                        <div class="action">
                            <span @click="openBoard(board.id)">open</span>
                            <span @click="editBoard(board)">edit</span>
                            <span @click="deleteBoard(board)">delete</span>
                        </div>
                    </form>
                </div>

                <div class="board-wrapper" v-if="action === 'add board'">
                    <form class="board new-board" @submit.prevent="saveBoard">
                        <!-- use Enter to submit create -->
                        <input type="text" placeholder="New Board Name" autofocus v-model="new_board.name">
                    </form>
                </div>
                <div class="board-wrapper" v-else @click="action = 'add board'">
                    <div class="board new-board">Create new board...</div>
                </div>

            </div>

            <div v-if="tab === 'list'">
                <div class="team-container">
                    <div class="board-name">{{ board.name }}</div>
                    <div class="member" v-for="member in board.members"
                         :title="member.first_name + ' ' + member.last_name"
                         @click="removeMember(member)">
                        {{ member.initial }}
                    </div>

                    <a href="javascript:void(0)" class="button" v-if="action === 'new member'">
                        <!-- use Enter to submit add new member -->
                        <input type="text" placeholder="Username" autofocus v-model="member.username"
                               @keyup.enter="addNewMember">
                    </a>

                    <a href="" class="button" v-else @click.prevent="newMember">+ Add member</a>
                </div>
                <div class="card-container">
                    <div class="content">
                        <div class="list" v-for="list in board.lists">
                            <header>
                                <span v-if="action === 'edit list' && list.id === new_list.id">
                                    <input type="text" value="Backlog" placeholder="Are you sure want to delete?"
                                           autofocus v-model="new_list.name" @keyup.enter="saveList">
                                </span>
                                <span v-else>{{ list.name }}</span>
                                <div class="action">
                                    <span @click="editList(list)">edit</span>
                                    <span @click="deleteList(list)">delete</span>
                                </div>
                            </header>

                            <div class="cards">
                                <div class="card" v-for="card in list.cards">
                                    <div class="card-content">

                                        <input type="text" v-model="new_card.task"
                                               placeholder="Are you sure want to delete?" autofocus
                                               v-if="action === 'edit card' && card.id === new_card.id" @keyup.enter="saveCard">
                                        <span v-else>{{ card.task }}</span>

                                        <div class="action">
                                            <span @click="editCard(card)">edit</span>
                                            <span @click="deleteCard(card)">delete</span>
                                        </div>
                                    </div>
                                    <div class="control">
                                        <span @click="moveUp(card)">&uarr;</span>
                                        <span @click="moveDown(card)">&darr;</span>
                                    </div>
                                </div>
                            </div>

                            <div class="button" v-if="action === 'new card' && new_card.list_id === list.id">
                                <!-- use Enter to submit create -->
                                <input type="text" placeholder="New card" autofocus v-model="new_card.task"
                                       @keyup.enter="saveCard">
                            </div>
                            <div class="button" v-else @click="newCard(list)">+ Add new card</div>
                            <div class="control">
                                <span @click="moveLeft(list)">&larr;</span>
                                <span @click="moveRight(list)">&rarr;</span>
                            </div>
                        </div>
                        <div class="list button">
                            <span v-if="action === 'new list'">
                                <input type="text" placeholder="New List Name" autofocus v-model="list.name"
                                       @keyup.enter="saveList">
                            </span>
                            <span v-else @click="newList">+ Add another list</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script>
    import api from './api'

    export default {
        name: 'app',
        components: {},
        data: function () {
            return {
                user: JSON.parse(localStorage.getItem('user')),
                login: {},
                register: {},
                tab: 'board',
                boards: [],
                board: {},
                new_board: {},
                new_list: {},
                new_card: {},
                action: null,
            }
        },
        watch: {
            tab(value) {
                if (value === 'board') this.getBoard()
            }
        },
        mounted() {
            if (this.user) {
                this.getBoard()
            }
        },
        methods: {
            setUser(data) {
                this.user = data
                if (data) localStorage.setItem('user', JSON.stringify(data))
                else {
                    this.user = null
                    localStorage.removeItem('user')
                }
            },
            doLogin() {
                api.post('auth/login', this.login, (res) => {
                    if (res.status) {
                        this.setUser(res.data)
                        this.login = {}
                    }
                })
            },
            doRegister() {
                api.post('auth/register', this.region, (res) => {
                    if (res.status) {
                        this.setUser(res.data)
                        this.login = {}
                    }
                })
            },
            doLogout() {
                api.get('auth/logout', (res) => {
                    if (res.status) {
                        this.setUser()
                    }
                })
            },
            getBoard() {
                api.get('board', (res) => {
                    if (res.status) this.boards = res.data
                })
            },
            editBoard(board) {
                this.new_board = JSON.parse(JSON.stringify(board))
                this.action = 'edit board'
            },
            saveBoard() {
                let url = 'board'

                if (this.new_board.id) {
                    url += '/' + this.new_board.id
                    this.new_board._method = 'PUT'
                }

                api.post(url, this.new_board, (res) => {
                    this.action = null
                    this.new_board = {}
                    this.getBoard()
                })
            },
            deleteBoard(board) {
                api.post('board/' + board.id, {_method: 'DELETE'}, (res) => {
                    if (res.status) {
                        this.action = null
                        this.new_board = {}
                        this.getBoard()
                    }
                })
            },
            openBoard(id) {
                this.new_list = {}
                this.action = null
                api.get('board/' + id, (res) => {
                    if (res.status) {
                        this.board = res.data
                        this.board.lists.sort((a, b) => {
                            return a.order - b.order
                        })

                        this.board.lists.map((list) => {
                            list.cards.sort((a, b) => {
                                return a.order - b.order
                            })
                        })

                        this.tab = 'list'
                    }
                })
            },
            moveLeft(list) {
                api.post(`board/${this.board.id}/list/${list.id}/left`, {}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            moveRight(list) {
                api.post(`board/${this.board.id}/list/${list.id}/right`, {}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            moveUp(card) {
                api.post(`card/${card.id}/up`, {}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            moveDown(card) {
                api.post(`card/${card.id}/down`, {}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            newMember() {
                this.action = 'new member'
                this.member = {}
            },
            addNewMember() {
                api.post(`board/${this.board.id}/member`, this.member, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            removeMember(member) {
                api.post(`board/${this.board.id}/member/${member.id}`, {_method: 'DELETE'}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            newList() {
                this.action = 'new list'
                this.new_list = {}
            },
            editList(list) {
                this.new_list = JSON.parse(JSON.stringify(list))
                this.action = 'edit list'
            },
            saveList() {
                let url = `board/${this.board.id}/list`

                if (this.new_list) {
                    url += '/' + this.new_list.id
                    this.new_list._method = 'PUT'
                }

                api.post(url, this.new_list, (res) => {
                    this.openBoard(this.board.id)
                })
            },
            deleteList(list) {
                api.post(`board/${this.board.id}/list/${list.id}`, {_method: 'DELETE'}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
            newCard(list) {
                this.action = 'new card'
                this.new_card = {
                    list_id: list.id
                }
            },
            editCard(card) {
                this.new_card = JSON.parse(JSON.stringify(card))
                this.action = 'edit card'
            },
            saveCard() {
                let url = `board/${this.board.id}/list/${this.new_card.list_id}/card`

                if (this.new_card.id) {
                    url += '/' + this.new_card.id
                    this.new_card._method = 'PUT'
                }

                api.post(url, this.new_card, (res) => {
                    this.openBoard(this.board.id)
                })
            },
            deleteCard(card) {
                api.post(`board/${this.board.id}/list/${card.list_id}/card/${card.id}`, {_method: 'DELETE'}, (res) => {
                    if (res.status) this.openBoard(this.board.id)
                })
            },
        }
    }
</script>

<style>
</style>
