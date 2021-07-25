/*
 * @author Marin Taverniers
 */

export {Ajax};

class Ajax {
    static getText(url, method, body) {
        return Ajax.get(url, method, body, false);
    }

    static getJson(url, method, body) {
        return Ajax.get(url, method, body, true);
    }

    static get(url, method, body, isJson = false) {
        let headers = {"X-Requested-With": "XMLHttpRequest"}; // Needed for Symfony controllers
        let options = {method: method, headers: headers, body: body};
        return fetch(url, options)
            .then((response) => {
                if (response.ok || response.status === 422) {
                    if (isJson) {
                        response = response.json();
                    } else {
                        response = response.text();
                    }
                    return Promise.resolve(response);
                }
                console.error(response.statusText);
                return Promise.reject(response.statusText);
            })
            .catch((error) => {
                return Promise.reject(error);
            });
    }
}
