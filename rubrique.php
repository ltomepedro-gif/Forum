<?php
session_start(); // Démarrer la session pour accéder aux données utilisateur

$host = 'localhost';
$bdd = 'forum';
$user = 'root';
$passwd = 'root';

try {
    $cnn = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $user, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(PDOException $e) {
    echo 'Erreur : '.$e->getMessage();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_article']) && $_SESSION['typeMemb'] == 1) {
    $idArtToDelete = intval($_POST['idArt']);
    
    // Supprimer les réponses liées à l'article
    $cnn->prepare("DELETE FROM reponse WHERE idArt = ?")->execute([$idArtToDelete]);
    
    // Supprimer l'article
    $cnn->prepare("DELETE FROM article WHERE idArt = ?")->execute([$idArtToDelete]);
    
    // Redirection pour éviter le repost
    header("Location: rubrique.php?idRub=" . $_GET['idRub']);
    exit;
}

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Fonction pour formater la date
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 1) {
        return 'hier';
    } elseif ($diff->d == 2) {
        return 'avant-hier';
    } elseif ($diff->d > 2 && $diff->d < 7) {
        return 'il y a ' . $diff->d . ' jours';
    } elseif ($diff->d >= 7) {
        return 'le ' . $ago->format('d F');
    } elseif ($diff->h > 0) {
        return 'il y a ' . $diff->h . ' ' . ($diff->h == 1 ? 'heure' : 'heures');
    } elseif ($diff->i > 0) {
        return 'il y a ' . $diff->i . ' ' . ($diff->i == 1 ? 'minute' : 'minutes');
    } else {
        return 'à l’instant';
    }
}
if (isset($_GET['idRub'])) {
    $idRub = $_GET['idRub'];

    $queryRubrique = "SELECT * FROM rubrique WHERE idRub = :idRub";
    $stmtRubrique = $cnn->prepare($queryRubrique);
    $stmtRubrique->bindParam(':idRub', $idRub);
    $stmtRubrique->execute();
    $rubrique = $stmtRubrique->fetch(PDO::FETCH_ASSOC);

    if ($rubrique) {
        $queryCategorie = "SELECT * FROM categorie WHERE idCat = :idCat";
        $stmtCategorie = $cnn->prepare($queryCategorie);
        $stmtCategorie->bindParam(':idCat', $rubrique['idCat']);
        $stmtCategorie->execute();
        $categorie = $stmtCategorie->fetch(PDO::FETCH_ASSOC);

        $queryArticles = "
SELECT 
    a.*, 
    m.nomMemb AS auteur_nom, 
    m.prenomMemb AS auteur_prenom, 
    m.typeMemb AS auteur_type, 
    a.dateArt, 
    (SELECT COUNT(*) FROM reponse r WHERE r.idArt = a.idArt) AS nombre_messages
FROM article a
JOIN membre m ON a.idMemb = m.idMemb
WHERE a.idRub = :idRub
ORDER BY a.dateArt DESC
";




        $stmtArticles = $cnn->prepare($queryArticles);
        $stmtArticles->bindParam(':idRub', $idRub);
        $stmtArticles->execute();
        $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Rubrique non trouvée.";
        exit;
    }
} else {
    echo "ID de rubrique manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($rubrique['nomRub']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFEDDE;
            color: #FFFFFF;
        }
        header {
            background-color: #BEA692;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #fff;
        }
        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin: 0;
            color: white;
        }
        .buttons a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .buttons a:hover {
            background-color: white;
            color: #4a352a;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #BEA692;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .rubric h1, .rubric p, .article-title a {
            color: white;
        }
        .rubric h1 {
            font-size: 28px;
            font-family: 'Playfair Display', serif;
        }
        .rubric p {
            font-size: 16px;
        }
        .articles-list {
            margin-top: 15px;
        }
        .article-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .article-content {
            display: flex;
            align-items: center;
        }
        .article-icon {
            font-size: 20px;
            margin-right: 5px;
        }
        .article-title a {
            font-style: italic;
            text-decoration: none;
            font-size: 16px;
        }
        .article-title a:hover {
            color: #e0c9a3;
        }
        .article-meta {
            font-size: 14px;
            text-align: right;
            color: #5A5858;
        }
        a.return-button {
            display: inline-block;
            margin-top: 15px;
            color: white;
            text-decoration: none;
            background-color: #4a352a;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        a.return-button:hover {
            background-color: white;
            color: #4a352a;
        }
        .avatar {
    display: inline-block;
    width: 20px;
    height: 20px;
    background-color: #5A5858;
    color: white;
    font-size: 14px;
    text-align: center;
    line-height: 20px;
    border-radius: 50%;
    margin-left: 5px;
}
.buttons-container {
    display: flex;
    justify-content: space-between; /* Aligne les boutons aux extrémités */
    align-items: center;
    margin-top: 15px;
}
.return-button,
.add-article-button {
    background-color: #4a352a;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    transition: 0.3s;
    display: inline-block;
}

.return-button:hover,
.add-article-button:hover {
    background-color: white;
    color: #4a352a;
}
.stats {
    text-align: center;
    background: #BEA692;
    padding: 1px 10px;
    border-top: 2px solid white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stats-container {
    display: flex;
    justify-content: space-between;
    width: 80%;
    max-width: 1000px;
}

.stats-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 18px;
}

.stats-item span {
    font-size: 50px; /* Agrandit les emojis */
}

.stats-item p {
    margin: 5px 0 0;
    font-size: 16px;
    font-weight: bold;
}

.last-member {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 25%
}
.last-member .date {
    font-size: 12px; /* Réduit la taille de la date */
    color: #444;
    margin-top: 5px;
    text-align: center;
}
        footer {
            text-align: center;
            padding: 1px;
            font-size: 14px;
            background: #4a352a;
        }
        .avatar {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: #5A5858;
            color: white;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            line-height: 30px;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .last-member-info {
            display: flex;
    flex-direction: column; /* Mets "Membre le plus récent" au-dessus */
    align-items: center;
    text-align: center;
    font-size: 18px;
}
.name-avatar {
    display: flex;
    align-items: center;
    gap: 1px; /* Ajuste l'espace entre l'avatar et le nom */
    justify-content: center;
}

        .last-member .avatar {
            width: 30px;
    height: 30px;
    font-size: 16px;
    line-height: 30px;
    border-radius: 50%;
    text-align: center;
    background-color: #5A5858;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;

}




    </style>
</head>
<body>
<header>
<h1><?php echo htmlspecialchars($rubrique['nomRub']); ?></h1>
<div class="buttons">
    <?php if (isset($_SESSION['prenomMemb'])): ?>
        <?php if (isset($_SESSION['typeMemb']) && $_SESSION['typeMemb'] == 0): ?>
            <a href="admin.php" class="btn">Administration</a>
        <?php endif; ?>
        
        <p class="user-name">
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
            </strong>!
        </p>
        <a href="Deconnexion.php" class="btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Déconnexion</a>
    <?php else: ?>
        <a href="Seconnecter.php">Connexion</a>
        <a href="Incription.php">Inscription</a>
    <?php endif; ?>
</div>

</header>

    <div class="container">
        <div class="rubric">
            <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($categorie['nomCat']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($rubrique['descRub'])); ?></p>
            <h2>Articles :</h2>
            <div class="articles-list">
                <?php if (empty($articles)): ?>
                    <p>Pas d'article pour le moment</p>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
    <div class="article-item">
        <div class="article-content">
            <span class="article-icon">💬</span>
            <div class="article-title" style="display: flex; align-items: center; gap: 10px;">
    <a href="article.php?idArt=<?php echo $article['idArt']; ?>&idRub=<?php echo $idRub; ?>">
        <?php echo htmlspecialchars($article['titreArt']); ?>
    </a>
    
    <?php if (isset($_SESSION['typeMemb']) && $_SESSION['typeMemb'] == 1): ?>
        <form method="POST" onsubmit="return confirm('Supprimer cet article ?')" style="display:inline;">
            <input type="hidden" name="idArt" value="<?php echo $article['idArt']; ?>">
            <button type="submit" name="supprimer_article" style="background-color: crimson; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Supprimer</button>
        </form>
    <?php endif; ?>
</div>

        </div>
        <span class="article-meta">
    <strong><?php echo $article['nombre_messages']; ?></strong> <?php echo $article['nombre_messages'] <= 1 ? 'message' : 'messages'; ?>
    - Par <span style="color: <?php
    if ($article['auteur_type'] == 0) {
        echo 'red';
    } elseif ($article['auteur_type'] == 1) {
        echo 'blue';
    } elseif ($article['auteur_type'] == 2) {
        echo 'black';
    }
    
?>">
    <?php echo htmlspecialchars($article['auteur_prenom']) . ' ' . htmlspecialchars($article['auteur_nom']); ?>
</span>

            </span>
        </span>
    </div>
<?php endforeach; ?>

                <?php endif; ?>
            </div>
            <div class="buttons-container">
    <a href="index.php" class="return-button">Retour à l'accueil</a>
    <?php if (isset($_SESSION['prenomMemb'])): ?>
        <a href="ajouter_article.php?idRub=<?php echo $idRub; ?>" class="add-article-button">Ajouter un article</a>
        
    <?php endif; ?>
</div>
    </div>
    <?php
// Récupérer les statistiques mises à jour
$queryStats = "
    SELECT 
        (SELECT COUNT(*) FROM article) AS total_articles,
        (SELECT COUNT(*) FROM reponse) AS total_messages,
        (SELECT COUNT(*) FROM membre) AS total_membres,
        (SELECT prenomMemb FROM membre ORDER BY dateIns DESC LIMIT 1) AS dernier_membre,
        (SELECT dateIns FROM membre ORDER BY dateIns DESC LIMIT 1) AS date_inscription_dernier_membre,
        (SELECT nomMemb FROM membre ORDER BY dateIns DESC LIMIT 1) AS dernier_nom_membre
";
$stmtStats = $cnn->query($queryStats);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

// Générer l'avatar (première lettre du prénom en majuscule)
$avatarLettre = strtoupper(substr($stats['dernier_membre'], 0, 1));
?>
<div class="stats">
    <h3>Statistiques du Forum</h3>
    <div class="stats-container">
        <div class="stats-item">
            <span>📄</span>
            <p>Total des articles : <strong><?php echo $stats['total_articles']; ?></strong></p>
        </div>
        <div class="stats-item">
            <span>💬</span>
            <p>Total des messages : <strong><?php echo $stats['total_messages']; ?></strong></p>
        </div>
        <div class="stats-item">
            <span>👥</span>
            <p>Total des membres : <strong><?php echo $stats['total_membres']; ?></strong></p>
        </div>
        <div class="stats-item last-member">
            <span style="color: blue; font-weight: bold;">🆕</span>
            <div class="last-member-info">
                <p><strong>Membre le plus récent :</strong></p>
                <div class="name-avatar">
                    <span class="avatar"><?php echo strtoupper(substr($stats['dernier_nom_membre'], 0, 1)); ?></span>
                    <p><?php echo htmlspecialchars($stats['dernier_nom_membre']); ?></p>
                </div>
            </div>
            <p class="date"><strong>Inscrit depuis le :</strong> <?php echo date('d F Y', strtotime($stats['date_inscription_dernier_membre'])); ?></p>
        </div>
    </div>
</div>

<footer>
    &copy; 2025 Lycée Mathias - Forum BTS SIO
</footer>



</body>
</html>
