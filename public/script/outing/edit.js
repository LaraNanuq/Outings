/*
 * @author Marin Taverniers
 */

import {Ajax} from "../util/Ajax.js";
import {Document} from "../util/Document.js";

(function () {
    let streetText;
    let latitudeText;
    let longitudeText;
    let savedLocationFormGroup;
    let savedLocationInput;
    let newLocationFormGroup;
    let newLocationCheckbox;

    Document.onReady(() => {
        // Saved location update
        streetText = document.querySelector("#street");
        latitudeText = document.querySelector("#latitude");
        longitudeText = document.querySelector("#longitude");
        savedLocationFormGroup = document.querySelector("#location-form-group");
        savedLocationInput = document.querySelector("#edit_outing_form_location");
        savedLocationInput.addEventListener("change", updateSavedLocationTexts);
        updateSavedLocationTexts();

        // New location update
        newLocationFormGroup = document.querySelector("#new-location-form-group");
        newLocationCheckbox = document.querySelector("#edit_outing_form_isNewLocation");
        newLocationCheckbox.addEventListener("change", updateLocationGroupsState);
        updateLocationGroupsState();

        // City update
        let form = document.querySelector("form[name='edit_outing_form']");
        let cityInput = document.querySelector("#edit_outing_form_newLocation_city");
        cityInput.addEventListener("change", function () {
            newLocationCheckbox.disabled = true;
            // Do not disable the field, or the form group, to prevent form submission while loading
            savedLocationInput.innerHTML = "<option value>- CHARGEMENT -</option>";
            resetSavedLocationTexts();

            // Get and replace saved locations
            let formData = new FormData(form);
            Ajax.getText(form.action, form.method, formData)
                .then((data) => {
                    Document.replaceElement("#" + savedLocationInput.id, data);
                })
                .catch((error) => {
                    savedLocationInput.innerHTML = "<option value>- An error occurred -</option>";
                })
                .finally(() => {
                    updateLocationGroupsState();
                    newLocationCheckbox.disabled = false;
                });
        });
    });

    function updateSavedLocationTexts() {
        let selectedLocationOption = savedLocationInput.options[savedLocationInput.selectedIndex];
        if (!selectedLocationOption.value) {
            resetSavedLocationTexts();
            return;
        }
        streetText.innerText = selectedLocationOption.getAttribute("street");
        latitudeText.innerText = selectedLocationOption.getAttribute("latitude");
        longitudeText.innerText = selectedLocationOption.getAttribute("longitude");
    }

    function resetSavedLocationTexts() {
        streetText.innerText = "...";
        latitudeText.innerText = "...";
        longitudeText.innerText = "...";
    }

    function updateLocationGroupsState() {
        savedLocationFormGroup.disabled = newLocationCheckbox.checked;
        newLocationFormGroup.disabled = !newLocationCheckbox.checked;
    }
})();
