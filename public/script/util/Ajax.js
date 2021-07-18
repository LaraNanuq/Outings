/*
 * @author Marin Taverniers
 */

export { Ajax };

class Ajax {
    static fetch(url, method, body, success, error = null) {
        return fetch(url, { method: method, body: body })
            .then((response) => {
                //    if (response.ok) {
                return Promise.resolve(response.text());
                //    }
                //    return Promise.reject(response.statusText);
            })
            .then((data) => {
                success(data);
            })
            .catch((e) => {
                console.error(e);
                if (error) {
                    error(e);
                }
            });
    }
}
