<?php
session_start();

$host = 'localhost';
$bdd = 'forum';
$user = 'root';
$passwd = 'root';

try {
    $cnn = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $user, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(PDOException $e) {
    echo 'Erreur : '.$e->getMessage();
    exit();
}
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMemb'])) {
        $id = $_POST['idMemb'];
    
        if (isset($_POST['supprimer'])) {
            $stmt = $cnn->prepare("DELETE FROM membre WHERE idMemb = ?");
            $stmt->execute([$id]);
        } elseif (isset($_POST['promouvoir'])) {
            $stmt = $cnn->prepare("UPDATE membre SET typeMemb = 1 WHERE idMemb = ?");
            $stmt->execute([$id]);
        } elseif (isset($_POST['retrograder'])) {
            $stmt = $cnn->prepare("UPDATE membre SET typeMemb = 2 WHERE idMemb = ?");
            $stmt->execute([$id]);
        }
    
        // Rafraîchir pour éviter le repost
        header("Location: admin.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouterRubrique'])) {
        $nomRub = trim($_POST['nomRub']);
        $descRub = trim($_POST['descRub']);
        $idCat = intval($_POST['idCat']);
    
        if ($nomRub && $descRub && $idCat) {
            $stmt = $cnn->prepare("INSERT INTO rubrique (nomRub, descRub, idCat) VALUES (?, ?, ?)");
            $stmt->execute([$nomRub, $descRub, $idCat]);
    
            // Pour éviter le repost
            header("Location: admin.php");
            exit();
        }
    }
    // SUPPRIMER une rubrique
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer_rubrique'])) {
        $idRub = intval($_POST['idRub']);

        // Supprimer toutes les réponses liées aux articles de la rubrique
        $stmt = $cnn->prepare("DELETE FROM reponse WHERE idArt IN (SELECT idArt FROM article WHERE idRub = ?)");
        $stmt->execute([$idRub]);

        // Supprimer tous les articles de la rubrique
        $stmt = $cnn->prepare("DELETE FROM article WHERE idRub = ?");
        $stmt->execute([$idRub]);

        // Supprimer la rubrique
        $stmt = $cnn->prepare("DELETE FROM rubrique WHERE idRub = ?");
        $stmt->execute([$idRub]);

        header("Location: admin.php");
        exit();
    }

    // MODIFIER une rubrique
    if (isset($_POST['modifier_rubrique'])) {
        $idRub = intval($_POST['idRub']);
        $nomRub = trim($_POST['nomRub']);
        $descRub = trim($_POST['descRub']);
        $idCat = intval($_POST['idCat']);

        $stmt = $cnn->prepare("UPDATE rubrique SET nomRub = ?, descRub = ?, idCat = ? WHERE idRub = ?");
        $stmt->execute([$nomRub, $descRub, $idCat, $idRub]);

        header("Location: admin.php");
        exit();
    }
}

    
    

// 🔒 Empêche l’accès à cette page si l’utilisateur n’est pas admin
if (!isset($_SESSION['typeMemb']) || $_SESSION['typeMemb'] != 0) {
    // Suppression utilisateur
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $cnn->prepare("DELETE FROM membre WHERE idMemb = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit();
}

// Promotion utilisateur
if (isset($_POST['promote_id'])) {
    $id = $_POST['promote_id'];
    $stmt = $cnn->prepare("UPDATE membre SET typeMemb = 1 WHERE idMemb = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit();
}

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
body {
    background-color: #FFEDDE;
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
}

header {
    background-color: #BEA692;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid white;
    position: relative;
}

.title {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: bold;
    color: white;
    flex: 1;
    text-align: center;
}

.buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.buttons a {
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border: 1px solid white;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s ease;
}

.buttons a:hover {
    background-color: white;
    color: #4a352a;
}

a.retour {
    position: absolute;
    left: 20px;
    top: 20px;
    text-decoration: none;
    color: white;
    font-weight: bold;
    background-color: #4a352a;
    padding: 8px 12px;
    border-radius: 5px;
    transition: 0.3s;
}

a.retour:hover {
    background-color: white;
    color: #4a352a;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.admin-section {
    margin-top: 20px;
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.toggle-title {
    font-weight: bold;
    font-size: 20px;
    cursor: pointer;
    margin-bottom: 10px;
    padding: 10px;
    background-color: #F0D9C1;
    border-radius: 8px;
    color: #4a352a;
    transition: background-color 0.3s ease;
}

.toggle-title:hover {
    background-color: #e2c5ac;
}

.member-list, .rubrique-list, .ajout-rubrique-form {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease, padding 0.3s ease;
    background-color: #ffffff;
    border-radius: 10px;
    margin-top: 10px;
    padding: 0 15px;
}

.member-list.open,
.rubrique-list.open,
.ajout-rubrique-form.open {
    max-height: 2000px;
    padding: 20px;
}

ul {
    list-style: none;
    padding: 0;
}

li {
    margin-bottom: 15px;
    font-size: 15px;
}

button {
    background-color: #4a352a;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 5px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #6a4e3a;
}

button[name="supprimer"],
button[name="supprimer_rubrique"] {
    background-color: crimson;
}

button[name="supprimer"]:hover,
button[name="supprimer_rubrique"]:hover {
    background-color: darkred;
}

input, textarea, select {
    font-family: 'Inter', sans-serif;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 8px;
    width: 100%;
    margin-bottom: 10px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}
.forum-title {
    font-family: 'Playfair Display', serif;
    font-size: 40px; /* ou 32px si tu veux plus grand */
    font-weight: bold;
    color: white;
    text-align: center;
    margin: 0 auto;
}


    </style>
</head>
<body>

<header>
<a href="index.php" style="
    position: absolute;
    left: 20px;
    top: 20px;
    text-decoration: none;
    color: white;
    font-weight: bold;
    background-color: #4a352a;
    padding: 8px 12px;
    border-radius: 5px;
    transition: 0.3s;
">
    ← Retour
</a>

    <h1 class="forum-title">Administration</h1>
    <div class="buttons">
        
        <?php if (isset($_SESSION['prenomMemb'])): ?>
            Bonjour, 
            <strong style="color: <?= ($_SESSION['typeMemb'] == 0 ? 'red' : ($_SESSION['typeMemb'] == 1 ? 'blue' : 'black')) ?>;">
                <?= htmlspecialchars($_SESSION['prenomMemb']) ?>
            </strong> !
            <a href="Deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <a href="Seconnecter.php">Connexion</a>
        <?php endif; ?>
    </div>
</header>


<div class="container">
    <div class="admin-section">
        <div class="toggle-title" onclick="toggleMembres()">
        📌 Gestion des membres
    </div>

    <div id="liste-membres" class="member-list">
    <ul style="list-style: none; padding-left: 0;">
        <?php
        $stmt = $cnn->query("SELECT idMemb, prenomMemb, nomMemb, typeMemb, dateIns FROM membre ORDER BY dateIns DESC");
        while ($membre = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $color = $membre['typeMemb'] == 0 ? 'red' : ($membre['typeMemb'] == 1 ? 'blue' : 'black');
            echo "<li style='color: $color; margin-bottom: 10px;'>
                <form method='POST' style='display: inline;'>
                    <strong>" . htmlspecialchars($membre['prenomMemb']) . " " . htmlspecialchars($membre['nomMemb']) . "</strong> 
                    - inscrit le " . date('d/m/Y', strtotime($membre['dateIns'])) . "
                    <input type='hidden' name='idMemb' value='" . $membre['idMemb'] . "'>";

            // Bouton promouvoir ou rétrograder
            if ($membre['typeMemb'] == 2) {
                echo "<button type='submit' name='promouvoir'>Promouvoir</button> ";
            } elseif ($membre['typeMemb'] == 1) {
                echo "<button type='submit' name='retrograder'>Rétrograder</button> ";
            }

            // Bouton supprimer (sauf si admin)
            if ($membre['typeMemb'] != 0) {
                echo "<button type='submit' name='supprimer' onclick=\"return confirm('Supprimer ce membre ?')\">Supprimer</button>";
            }

            echo "</form></li>";
        }
        ?>
    </ul>
</div>
<div class="admin-sectionn">
    <div class="toggle-title" onclick="toggleRubriques()">
        📌 Gestion des rubriques
    </div>

    <div id="liste-rubriques" class="rubrique-list">
<?php
$query = "
    SELECT r.idRub, r.nomRub, r.descRub, r.idCat, c.nomCat
    FROM rubrique r
    JOIN categorie c ON r.idCat = c.idCat
    ORDER BY c.nomCat, r.nomRub
";
$stmt = $cnn->query($query);

$rubriquesParCategorie = [];
while ($rub = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rubriquesParCategorie[$rub['nomCat']][] = $rub;
}

foreach ($rubriquesParCategorie as $categorie => $rubriques) {
    echo "<h4 style='margin-top: 20px;'>📂 <u>" . htmlspecialchars($categorie) . "</u></h4><ul style='list-style: none; padding-left: 0;'>";

    foreach ($rubriques as $rub) {
        $rubId = $rub['idRub'];
        echo "<li style='margin-bottom: 10px;'>
            <strong>" . htmlspecialchars($rub['nomRub']) . "</strong> : " . htmlspecialchars($rub['descRub']) . "<br>
            <form method='POST' style='display: inline-block; margin-top: 5px;'>
                <input type='hidden' name='idRub' value='" . $rubId . "'>
                <button type='submit' name='supprimer_rubrique' style='color: white; background-color: crimson; border: none; padding: 5px 10px; border-radius: 5px;'>Supprimer</button>
            </form>

            <button onclick=\"toggleEditForm($rubId)\" style='margin-left: 10px; background-color: #4a352a; color: white; border: none; padding: 5px 10px; border-radius: 5px;'>Modifier</button>

            <div id='edit-form-$rubId' style='display: none; margin-top: 10px; background: #f4f4f4; padding: 10px; border-radius: 5px;'>
                <form method='POST'>
                    <input type='hidden' name='idRub' value='" . $rubId . "'>
                    <label>Titre :</label><br>
                    <input type='text' name='nomRub' value=\"" . htmlspecialchars($rub['nomRub']) . "\" required style='width: 100%; padding: 5px;'><br><br>

                    <label>Description :</label><br>
                    <textarea name='descRub' rows='3' required style='width: 100%; padding: 5px;'>" . htmlspecialchars($rub['descRub']) . "</textarea><br><br>

                    <label>Catégorie :</label><br>
                    <select name='idCat' required style='width: 100%; padding: 5px;'>";

                    $cats = $cnn->query("SELECT idCat, nomCat FROM categorie ORDER BY nomCat ASC");
                    while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($cat['idCat'] == $rub['idCat']) ? "selected" : "";
                        echo "<option value='" . $cat['idCat'] . "' $selected>" . htmlspecialchars($cat['nomCat']) . "</option>";
                    }

        echo "      </select><br><br>
                    <button type='submit' name='modifier_rubrique' style='background-color: #4a352a; color: white; border: none; padding: 5px 15px; border-radius: 5px;'>Enregistrer</button>
                </form>
            </div>
        </li>";
    }

    echo "</ul>";
}
?>


    <hr>
    <div class="toggle-title" onclick="toggleAjoutRubrique()">
    ➕ Ajouter une rubrique
</div>

<div id="form-ajout-rubrique" class="ajout-rubrique-form">
    <form method="POST" style="margin-top: 15px;">
        <label for="nomRub">Titre de la rubrique :</label><br>
        <input type="text" name="nomRub" id="nomRub" required style="width: 100%; padding: 8px;"><br><br>

        <label for="descRub">Description :</label><br>
        <textarea name="descRub" id="descRub" rows="4" required style="width: 100%; padding: 8px;"></textarea><br><br>

        <label for="idCat">Catégorie :</label><br>
        <select name="idCat" id="idCat" required style="width: 100%; padding: 8px;">
            <option value="">-- Sélectionner une catégorie --</option>
            <?php
            $stmt = $cnn->query("SELECT idCat, nomCat FROM categorie ORDER BY nomCat ASC");
            while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $cat['idCat'] . "'>" . htmlspecialchars($cat['nomCat']) . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" name="ajouterRubrique" style="background-color: #4a352a; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            Ajouter la rubrique
        </button>
    </form>
</div>
        </div>
    </div>


<script>
function toggleMembres() {
    const section = document.getElementById("liste-membres");
    section.classList.toggle("open");
}
function toggleRubriques() {
    const section = document.getElementById("liste-rubriques");
    section.classList.toggle("open");
}
function toggleAjoutRubrique() {
    const section = document.getElementById("form-ajout-rubrique");
    section.classList.toggle("open");
}
function toggleEditForm(id) {
    const div = document.getElementById('edit-form-' + id);
    if (div.style.display === 'none' || div.style.display === '') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
</script>

</body>
</html>
