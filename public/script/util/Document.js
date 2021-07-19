/*
 * @author Marin Taverniers
 */

export { Document };

class Document {
    static onReady(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback);
        } else {
            callback();
        }
    }

    static replaceElement(selector, newHtml) {
        let oldElement = document.querySelector(selector);
        let tempElement = document.createElement("div");
        tempElement.innerHTML = newHtml;
        let newElement = tempElement.querySelector(selector);
        oldElement.innerHTML = newElement.innerHTML;
        tempElement.remove();
    }
}
