const BASE_URL = 'http://localhost:8000/v1/'

const generateData = (data) => {
    let res = new FormData

    for (let k in data) {
        res.append(k, data[k])
    }
    return res
}

const getUrl = (url) => {
    let res = BASE_URL + url

    let user = JSON.parse(localStorage.getItem('user'))

    if (user) res += '?token=' + user.token

    return res
}

const api = {
    post(url, data = {}, callback) {
        data = generateData(data)

        fetch(getUrl(url), {
            body: data,
            method: 'POST',
        }).then((result) => {
            result.json().then((res) => {
                callback({
                    status: result.status === 200,
                    data: res
                })
            })
        })
    },
    get(url, callback) {
        fetch(getUrl(url)).then((result) => {
            result.json().then((res) => {
                callback({
                    status: result.status === 200,
                    data: res
                })
            })
        })
    }
}

export default api
