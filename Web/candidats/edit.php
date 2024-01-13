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
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const email = urlParams.get('email');

        fetch('http://localhost:8000/api/candidates?email=' + email)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)
                document.getElementById("email").value = data.email;
                document.getElementById("lastName").value = data.identity.lastName;
                document.getElementById("firstName").value = data.identity.firstName;
                document.getElementById("age").value = data.age;
                document.getElementById("address").value = data.address;

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

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const email = urlParams.get('email');

        formToObject.currentEmail = email
        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)
        fetch('http://localhost:8000/api/candidate/' + email, {
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
