<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-file"></i>
                <span>
                        <table id="formule-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h5>Formules</h5>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </span>
            </div>
        </div>

        <div class="contact-col">
            <form id="formule" action="">
                <label for="wording">Formule</label><input type="text" name="wording" id="wording" placeholder="Formule" required>
                <label for="price">Prix</label><input type="number" name="price" id="price" placeholder="Prix" min="0" required>
                <label for="minAge">Age minimal requis</label><input type="number" name="minAge" id="minAge" placeholder="âge minimal requis" min="16" max="200" required>
                <label for="isCodeFormula">Choisir une formule code ?</label><input type="checkbox" name="isCodeFormula" id="isCodeFormula" />

                <label for="nbHours">Nombre d'heure dans cette formule</label><input type="number" name="nbHours" id="nbHours" placeholder="Heures" min="1" required>

                <!--                <select id="vehiculeNumPlate" name="vehiculeNumPlate"></select>-->
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    // const apiUrls = "http://127.0.0.1:8000/api/vehicles";
    // fetch(apiUrls)
    //     .then(response => response.json())
    //     .then(data => {
    //         const vehiculeNumPlate = document.getElementById("vehiculeNumPlate");
    //         for (const item of data) {
    //             const option = document.createElement("option");
    //             option.value = item.numPlate;
    //             option.text = item.numPlate;
    //             vehiculeNumPlate.appendChild(option);
    //         }
    //     })
    //     .catch(error => console.error('Error:', error));

    function isFormulaCodeOrDriving(e) {
        console.log(e.currentTarget.checked)
        const nbHours = document.getElementById('nbHours')

        if(e.currentTarget.checked) {
            nbHours.setAttribute("disabled", "disabled");
        } else {
            nbHours.removeAttribute("disabled");
        }
    }
    const isFormuleCodeCheckBox = document.getElementById('isCodeFormula')
    isFormuleCodeCheckBox.addEventListener("click", isFormulaCodeOrDriving, false);

    async function getData() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const slug = urlParams.get('slug');

        await fetch('http://localhost:8000/api/formulas?slug=' + slug)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)
                document.getElementById("wording").value = data.wording;
                document.getElementById("price").value = data.price;
                document.getElementById("minAge").value = data.minAge;

                if(data.drivingFormula) {
                    document.getElementById("nbHours").value = data.drivingFormula.nbHours;
                } else {
                    document.getElementById("isCodeFormula").checked = true;
                    document.getElementById("nbHours").setAttribute("disabled", "disabled");
                }
            })
            .catch(error => console.error('Error:', error));
    }
    getData()

    const formula = document.getElementById('formule'); // get submit
    formula.addEventListener("submit", submitForm, false);
    function submitForm(e) {
        e.preventDefault();
        console.log(e.target);
        console.log(e.currentTarget);

        const formData = new FormData(e.currentTarget);
        const apiUrl = "http://localhost:8000/api";

        let formToObject = Object.fromEntries(formData.entries());
        formToObject.price = parseInt(formToObject.price)
        if(formToObject.nbHours) {
            formToObject.nbHours = parseInt(formToObject.nbHours)
        }
        formToObject.minAge = parseInt(formToObject.minAge)


        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const slug = urlParams.get('slug');

        formToObject.currentSlug = slug

        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)


        fetch(apiUrl + '/formula/' + slug, {
            method: 'PUT',
            mode: 'cors', // no-cors, *cors, same-origin
            headers: {
                'Content-Type': 'application/json',
                Accept: "application/json",
            },
            body: formToJson
        })
            .then(response => response.json())
            .then(data => {
                console.log(data)
                if(data?.violations === undefined) {
                    getData(); // update screen
                    document.getElementById('formule').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</html>
