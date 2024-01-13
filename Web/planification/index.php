<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-calendar"></i>
                <span>
                        <table id="courses-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h5><a href="/calendrier/">Voir planning</a></h5>
                                    </th>
                                </tr>
                                <tr>
                                     <th>
                                        <h5><a href="/calendrier/achieve.php">Voir planning des cours achevés</a></h5>
                                    </th>
                                </tr>
                                <tr>
                                     <th>
                                        <h5><a href="/calendrier/delete.php">Voir planning des annulations</a></h5>
                                    </th>
                                </tr>
                                <tr>
                                     <th>
                                        <h5><a href="/calendrier/toAchieve.php">Achever les cours</a></h5>
                                    </th>
                                </tr>

                                <tr>
                                     <th>
                                        <h5><a href="/calendrier/trainer.php">Voir planning côté moniteur (et annuler cours si besoin)</a></h5>
                                    </th>
                                </tr>

                                 <tr>
                                     <th>
                                        <h5><a href="/calendrier/justification.php">Annuler le cours pour une raison exceptionnelle</a></h5>
                                    </th>
                                </tr>

                                <tr>
                                     <th>
                                        <h5><a href="/calendrier/candidate.php">Voir planning côté candidat</a></h5>
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
            <form id="cours" action="">
                <label for="startDate">Date Début</label>
                <input type="datetime-local" name="startDate" id="startDate" placeholder="Début Date" required>
                <label for="endDate">Date Fin</label>
                <input type="datetime-local" name="endDate" id="endDate" placeholder="Fin Date" required>
                <label for="trainerNumSs">Moniteur</label><select id="trainerNumSs" name="trainerNumSs"></select>
                <label for="candidateEmail">Candidat</label><select id="candidateEmail" name="candidateEmail"></select>
                <label for="formulaSlug">Formule</label><select id="formulaSlug" name="formulaSlug"></select>
                <label for="vehicleNumPlate">Véhicule associé</label><select id="vehicleNumPlate" name="vehicleNumPlate"></select>
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    const min = (new Date()).toISOString().slice(0, 16);
    document.getElementById('startDate').min = min;
    document.getElementById('endDate').min = min;

    //-------------------------------------------------------------------------------------------------//
    async function getVehicleData() {
        const formula = document.getElementById("formulaSlug");
        const slug = formula.value;

        // disabled input if you have code formular
        if(formula.options[formula.options.selectedIndex].value === "formule-code-illimite" || formula.options[formula.options.selectedIndex].value === "formule-code") {
            document.getElementById("vehicleNumPlate").setAttribute('disabled', "disabled")
        } else {
            document.getElementById("vehicleNumPlate").removeAttribute("disabled")
        }

        await fetch("http://127.0.0.1:8000/api/vehicles?drivingFormulas.formula.slug=" + slug)
            .then(response => response.json())
            .then(data => {
                const vehicles = document.getElementById("vehicleNumPlate");
                const options = vehicles.querySelectorAll("option")
                if(options.length > 0) {
                    options.forEach(option => option.remove())
                }

                data.map(item => {
                    const option = document.createElement("option");
                    option.value = item.numPlate;
                    // option.text = item.numPlate + " => " + formula.options[formula.options.selectedIndex].text;
                    option.text = item.numPlate;
                    vehicles.appendChild(option);
                })


            })
            .catch(error => console.error('Error:', error));
    }

    //-----------------------------------------------------------------------------------------------------------//
    async function getTrainerData() {
        const formula = document.getElementById("formulaSlug");
        const slug = formula.value;

        await fetch("http://127.0.0.1:8000/api/trainers?formulas.slug=" + slug)
            .then(response => response.json())
            .then(data => {
                const trainers = document.getElementById("trainerNumSs");
                const options = trainers.querySelectorAll("option")
                if(options.length > 0) {
                    options.forEach(option => option.remove())
                }
                console.log(data)
                data.map(item => {
                    const option = document.createElement("option");
                    option.value = item.numSs;
                    option.text = item.identity.firstName + " " + item.identity.lastName + " => " + formula.options[formula.options.selectedIndex].text;
                    trainers.appendChild(option);
                })
            })
            .catch(error => console.error('Error:', error));
    }


    //-----------------------------------------------------------------------------------------------------------//
    function getEssentialData() {
        getVehicleData()
        getTrainerData()
    }

    fetch("http://127.0.0.1:8000/api/candidates")
        .then(response => response.json())
        .then(data => {
            const candidates = document.getElementById("candidateEmail");
            console.log(data)
            data.map(item => {
                const option = document.createElement("option");
                option.value = item.email;
                option.text = item.identity.firstName + " " + item.identity.lastName;
                candidates.appendChild(option);

                const formulaSlug = document.getElementById("formulaSlug");
                console.log(item)
                if(item.drivingFormulas) {
                    item.drivingFormulas.map(formula => {
                        const option = document.createElement("option");
                        option.value = formula.formula.slug;
                        option.text = formula.formula.wording;
                        formulaSlug.appendChild(option);
                    })
                }

                if(item.formulaCodeDates) {
                    item.formulaCodeDates.map(formula => {
                        const option = document.createElement("option");
                        option.value = formula.formula.slug;
                        option.text = formula.formula.wording;
                        formulaSlug.appendChild(option);
                    })
                }
            })

            getEssentialData()

        })
        .catch(error => console.error('Error:', error));


    document.getElementById("formulaSlug").addEventListener("change", getEssentialData, false);


    //-------------------------------------------------------------------------------------------------//

    function submitForm(e) {
        e.preventDefault();
        console.log(e.target);
        console.log(e.currentTarget);

        const formData = new FormData(e.currentTarget);

        let formToObject = Object.fromEntries(formData.entries());
        console.log(formToObject)

        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)

        fetch('http://localhost:8000/api/courseDate', {
            method: 'POST',
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
                if(data?.violations === undefined && data?.detail === undefined) {
                    document.getElementById('cours').reset() // reset form
                    getEssentialData()
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
    const course = document.getElementById('cours'); // get submit
    course.addEventListener("submit", submitForm, false);
</script>
</html>