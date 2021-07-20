/*
 * @author Marin Taverniers
 */

import { Ajax } from "../util/Ajax.js";
import { Document } from "../util/Document.js";

let edit = (function () {
    let streetText;
    let latitudeText;
    let longitudeText;
    let existingLocationFormGroup;
    let existingLocationInput;
    let newLocationFormGroup;
    let newLocationCheckbox;

    Document.onReady(() => {
        // Existing location update
        streetText = document.querySelector("#street");
        latitudeText = document.querySelector("#latitude");
        longitudeText = document.querySelector("#longitude");
        existingLocationFormGroup = document.querySelector("#existing-location-form-group");
        existingLocationInput = document.querySelector("#edit_outing_form_existingLocation");
        existingLocationInput.addEventListener("change", updateExistingLocationTexts);
        updateExistingLocationTexts();

        // New location update
        newLocationFormGroup = document.querySelector("#location-form-group");
        newLocationCheckbox = document.querySelector("#edit_outing_form_isNewLocation");
        newLocationCheckbox.addEventListener("change", updateLocationGroupsState);
        updateLocationGroupsState();

        // City update
        let form = document.querySelector("form[name='edit_outing_form']");
        let cityInput = document.querySelector("#edit_outing_form_location_city");
        cityInput.addEventListener("change", function () {
            newLocationCheckbox.disabled = true;
            existingLocationInput.disabled = true;
            existingLocationInput.innerHTML = "<option value>- CHARGEMENT -</option>";
            resetExistingLocationTexts();

            // Get new locations
            let formData = new FormData(form);
            Ajax.getText(form.action, form.method, formData)
                .then((data) => {
                    Document.replaceElement("#edit_outing_form_existingLocation", data);
                })
                .catch((error) => {
                    existingLocationInput.innerHTML = "<option value>- An error occurred -</option>";
                })
                .finally(() => {
                    updateLocationGroupsState();
                    newLocationCheckbox.disabled = false;
                    existingLocationInput.disabled = false;
                });
        });
    });

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

    function resetExistingLocationTexts() {
        streetText.innerText = "...";
        latitudeText.innerText = "...";
        longitudeText.innerText = "...";
    }

    function updateLocationGroupsState() {
        existingLocationFormGroup.disabled = newLocationCheckbox.checked;
        newLocationFormGroup.disabled = !newLocationCheckbox.checked;
    }
})();
