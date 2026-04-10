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
}

$idRub = isset($_GET['idRub']) ? $_GET['idRub'] : '';

// Récupération du nom de la rubrique
if (!empty($idRub)) {
    $stmt = $cnn->prepare("SELECT nomRub FROM rubrique WHERE idRub = ?");
    $stmt->execute([$idRub]);
    $rubrique = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomRub = $rubrique['nomRub'] ?? 'Rubrique Inconnue';
} else {
    $nomRub = 'Rubrique Inconnue';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = htmlspecialchars($_POST['titre']);
    $contenu = htmlspecialchars($_POST['contenu']);
    $idMemb = $_SESSION['idMemb'];
    $idRub = $_POST['idRub'];
    $dateArt = date('Y-m-d H:i:s');

    // Insérer l'article dans la base de données
    $sql = "INSERT INTO article (titreArt, dateArt, contenuArt, idMemb, idRub) VALUES (?, ?, ?, ?, ?)";
    $stmt = $cnn->prepare($sql);
    $stmt->execute([$titre, $dateArt, $contenu, $idMemb, $idRub]);

    header("Location: rubrique.php?idRub=" . $idRub);
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8eada;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #BEA692;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .logout-button {
            background-color: #BEA692;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-button:hover {
            background-color: #50392b;
        }
        .container {
            background: #c3a78f;
            padding: 30px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 80px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
        }
        h2 {
            color: #4a3222;
            font-size: 24px;
        }
        button {
            background: #4a3222;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background: #3a2418;
        }
        input, textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #4a3222;
            border-radius: 5px;
            font-size: 14px;
        }
        textarea {
            resize: none;
        }
        .buttons-container {
            margin-top: 20px;
        }
        .return-button {
            background-color: #4a352a;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
            display: inline-block;
            font-size: 14px;
        }
        .return-button:hover {
            background-color: white;
            color: #4a352a;
            border: 1px solid #4a352a;
        }
    </style>
</head>
<header style="background-color: #BEA692; position: relative; padding: 20px;">
    <h1 style="
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: bold;
        color: white;
        text-align: center;
        margin: 0;
    ">
        <?php echo htmlspecialchars($nomRub); ?>
    </h1>

    <div class="buttons" style="position: absolute; top: 20px; right: 20px; display: flex; gap: 10px; align-items: center;">
        <?php if (isset($_SESSION['prenomMemb'])): ?>
            <?php if ($_SESSION['typeMemb'] == 0): ?>
                <a href="admin.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Administration</a>
            <?php endif; ?>

            <p style="margin: 0; color: white;">
                Bonjour, 
                <strong style="color: <?php
                    if ($_SESSION['typeMemb'] == 0) {
                        echo 'red';
                    } elseif ($_SESSION['typeMemb'] == 1) {
                        echo 'blue';
                    } elseif ($_SESSION['typeMemb'] == 2) {
                        echo 'black';
                    }
                ?>">
                    <?php echo htmlspecialchars($_SESSION['prenomMemb']); ?>
                </strong> !
            </p>

            <a href="Deconnexion.php" class="logout-button" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Déconnexion</a>
        <?php else: ?>
            <a href="Seconnecter.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Connexion</a>
            <a href="Incription.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Inscription</a>
        <?php endif; ?>
    </div>
</header>

    
    <div class="container">
        <h2>Ajouter un article</h2>

        <div id="formulaire">
            <h3>Formulaire d'ajout</h3>
            <form action="" method="POST">
                <input type="text" name="titre" placeholder="Titre de l'article" required>
                <textarea name="contenu" rows="5" placeholder="Contenu de l'article" required></textarea>
                <input type="hidden" name="idRub" value="<?php echo htmlspecialchars($idRub); ?>">

                <button type="submit">Soumettre</button>
            </form>
        </div>
        <div class="buttons-container">
            <a href="index.php" class="return-button">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
