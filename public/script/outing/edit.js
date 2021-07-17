/*
 * @author Marin Taverniers
 */

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}

let streetText;
let latitudeText;
let longitudeText;
let existingLocationFormGroup;
let existingLocationInput;
let newLocationFormGroup;
let newLocationCheckbox;

function init() {

    // Existing location update
    streetText = document.querySelector("#street");
    latitudeText = document.querySelector("#latitude");
    longitudeText = document.querySelector("#longitude");
    existingLocationFormGroup = document.querySelector("#existing-location-form-group");
    existingLocationInput = document.querySelector("#edit_outing_form_existingLocation");
    existingLocationInput.addEventListener("change", updateExistingLocationTexts);
    updateExistingLocationTexts();

    // New location update
    newLocationFormGroup = document.querySelector("#new-location-form-group");
    newLocationCheckbox = document.querySelector("#edit_outing_form_isNewLocation");
    newLocationCheckbox.addEventListener("change", updateLocationGroups);
    updateLocationGroups();

    // City update
    let form = document.querySelector("form[name='edit_outing_form']");
    let cityInput = document.querySelector("#edit_outing_form_newLocation_city");
    //cityInput.disabled = false;
    cityInput.addEventListener("change", function () {
        existingLocationInput.innerHTML = "<option>- CHARGEMENT -</option>";
        resetExistingLocationTexts();

        // Get new locations
        let formData = new FormData(form);
        ajax(form.action, form.method, formData, (data) => {
            replaceElement("#edit_outing_form_existingLocation", data);
        });
    });
}

function resetExistingLocationTexts() {
    streetText.innerText = "...";
    latitudeText.innerText = "...";
    longitudeText.innerText = "...";
}

function updateExistingLocationTexts() {
    let selectedLocationOption = existingLocationInput.options[existingLocationInput.selectedIndex];
    if (!selectedLocationOption.value) {
        resetExistingLocationTexts();
        return;
    }
    streetText.innerText = selectedLocationOption.getAttribute("street");
    latitudeText.innerText = selectedLocationOption.getAttribute("latitude");
    longitudeText.innerText = selectedLocationOption.getAttribute("longitude");
}

function updateLocationGroups() {
    existingLocationFormGroup.disabled = newLocationCheckbox.checked;
    newLocationFormGroup.disabled = !newLocationCheckbox.checked;
}

function replaceElement(selector, htmlContent) {
    let oldElement = document.querySelector(selector);
    let tempElement = document.createElement("div");
    tempElement.innerHTML = htmlContent;
    let newElement = tempElement.querySelector(selector);
    oldElement.innerHTML = newElement.innerHTML;
    tempElement.remove();
    //oldElement.replaceWith(newElement);
}

function ajax(url, method, body, success, error = null) {
    fetch(url, { method: method, body: body })
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
