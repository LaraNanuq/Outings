/*
 * @author Marin Taverniers
 */

export { Document };

class Document {
    static onDocumentReady(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback);
        } else {
            callback();
        }
    }

    static replaceElement(selector, htmlContent) {
        let oldElement = document.querySelector(selector);
        let tempElement = document.createElement("div");
        tempElement.innerHTML = htmlContent;
        let newElement = tempElement.querySelector(selector);
        oldElement.innerHTML = newElement.innerHTML;
        tempElement.remove();
    }
}
