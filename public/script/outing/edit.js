if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}

function init() {
    let form = document.querySelector("form[name='edit_outing_form']");
    let cityInput = document.querySelector("#edit_outing_form_city");
    /*let streetText = document.querySelector('#street');
    let postalCodeText = document.querySelector('#postalCode');
    let latitudeText = document.querySelector('#latitude');
    let longitudeText = document.querySelector('#longitude');*/
    cityInput.addEventListener("change", function () {
        let oldLocationInput = document.querySelector("#edit_outing_form_location");
        oldLocationInput.disabled = true;
        
        oldLocationInput.innerHTML = "<option>- CHARGEMENT -</option>";
        /*streetText.innerText = '...';
        postalCodeText.innerText = '...';
        latitudeText.innerText = '...';
        longitudeText.innerText = '...';*/

        let formData = new FormData(form);
        fetch(form.action, { method: form.method, body: formData })
            .then((data) => {
                return Promise.resolve(data.text());
            })
            .then((data) => {
                let receivedHtml = document.createElement("div");
                receivedHtml.innerHTML = data;
                let newLocationInput = receivedHtml.querySelector("#edit_outing_form_location");
                oldLocationInput.replaceWith(newLocationInput);

                /*streetText.innerText = '...';
                postalCodeText.innerText = '...';
                latitudeText.innerText = '...';
                longitudeText.innerText = '...';*/

            })
            .catch();
    });
}
