<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="cours">
    <div class="row" style="flex-wrap: wrap;justify-content: center;"></div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    async function getData() {
        await fetch('http://localhost:8000/api/formulas')
            .then(response => response.json())
            .then(data => {
                    const row = document.querySelector(".row");

                    for (const item of data) {
                        const courseCol = document.createElement("div");
                        courseCol.className = "cours-col";
                        const h3 = document.createElement("h3");
                        h3.innerHTML = item.wording;
                        courseCol.appendChild(h3);
                        const p = document.createElement("p");
                        if(item?.drivingFormula?.nbHours > 0) {
                            p.innerHTML = "Ce service propose " + item.drivingFormula.nbHours + " h de cours.";
                        } else {
                            p.innerHTML = "Ce service est proposé avec une date.";
                        }
                        courseCol.appendChild(p);
                        row.appendChild(courseCol);
                    }

                }
            )
            .catch(error => console.error('Error:', error));
    }
    getData()
</script>
</html>



