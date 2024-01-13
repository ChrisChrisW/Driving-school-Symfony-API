<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-user-circle-o"></i>
                <span>
                        <table id="candidates-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h5>Candidats</h5>
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
            <form id="candidat" action="">
                <label for="lastName">Nom</label><input type="text" name="lastName" id="lastName" placeholder="Nom" required>
                <label for="firstName">Prénom</label><input type="text" name="firstName" id="firstName" placeholder="Prénom" required>
                <label for="age">Age</label><input type="number" name="age" id="age" placeholder="Age" min="16" required>
                <label for="email">Email</label><input type="email" name="email" id="email" placeholder="Email" pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.[a-zA-Z.]{2,15}" required>
                <label for="address">Adresse</label><input type="text" name="address" id="address" placeholder="Adresse" required>
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    //----------------------------------------------------------------------------------------------------------------//
    async function getData(){
        fetch('http://localhost:8000/api/candidates')
            .then(response => response.json())
            .then(data => {
                const table = document.getElementById("candidates-table");
                let tbody = table.getElementsByTagName("tbody")[0];
                if(tbody.childElementCount > 0) {
                    tbody.remove();
                    tbody = document.createElement("tbody");
                    table.appendChild(tbody);
                }

                data.map((item) => {
                    const row = document.createElement("tr");
                    const trainerCell = document.createElement("td");
                    trainerCell.innerHTML = item.identity.lastName + " " + item.identity.firstName + '<br><a href="edit.php?email=' + item.email + '"><i class="fa fa-pencil-square-o"></i> Modifier</a><br><a href="add-code.php?email=' + item.email + '"><i class="fa fa-car"></i>Ajouter des formules</a>';
                    row.appendChild(trainerCell);
                    tbody.appendChild(row);
                })
            })
            .catch(error => console.error('Error:', error));
    }
    getData()
    //-------------------------------------------------------------------------------------------------//
    function submitForm(e) {
        e.preventDefault();
        console.log(e.target);
        console.log(e.currentTarget);

        const formData = new FormData(e.currentTarget);

        let formToObject = Object.fromEntries(formData.entries());
        formToObject.age = parseInt(formToObject.age)

        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)

        fetch('http://localhost:8000/api/candidate', {
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
                if(data?.violations === undefined) {
                    getData(); // update screen
                    document.getElementById('candidat').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
    const candidat = document.getElementById('candidat'); // get submit
    candidat.addEventListener("submit", submitForm, false);
</script>
</html>
