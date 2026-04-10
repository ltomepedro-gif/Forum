<?php
session_start(); 

// Démarrer la session pour accéder aux données utilisateur


$host = 'localhost';
$bdd = 'forum';
$user = 'root';
$passwd = 'root';
try {
    $cnn = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $user, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(PDOException $e) {
    echo 'Erreur : '.$e->getMessage();
}

// Définir le fuseau horaire
date_default_timezone_set('UTC');

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
// Récupérer les catégories depuis la base de données
$query = "SELECT * FROM categorie";
$stmt = $cnn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum BTS SIO</title>
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
            text-align: center;
            border-bottom: 2px solid #fff;
            height: 100px;
        }
        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: #fff;
            margin: 0 auto;
            flex: 1;
        }
            .btn {
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border: 1px solid white;
    border-radius: 5px;
    transition: 0.3s;
    font-weight: bold;
}
.btn:hover {
    background-color: white;
    color: #4a352a;


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
            position: absolute;
            right: 20px;
            top: 53px; /* Assure que les boutons restent en haut */
            display: flex;
            gap: 10px;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            text-align: center;
        }
        .category {
            background: #BEA692;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 10px;
            color: white;
        }
        .rubric {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            color: white;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .rubric-info {
            flex: 1; /* Permet à la description de prendre plus d'espace */
            padding-right: 50px; /* Ajoute de l'espace à droite pour éloigner "Par ..." */
        }
        .rubric a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            transition: 0.3s;
            text-decoration: underline;
        }
        .date {
    font-size: 8px; /* Augmente la taille */
    font-weight: bold; /* Met en gras */
    color: #333; /* Couleur plus foncée pour lisibilité */
    text-align: center;
    margin-top: 5px;
}

        .rubric a:hover {
            color: #e0c9a3;
        }
        .rubric-meta {
            font-size: 14px;
            text-align: right;
            color: #5A5858;
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


<header style="display: flex; justify-content: space-between; align-items: center; background-color: #BEA692; padding: 20px; border-bottom: 2px solid #fff;">
    <h1 style="margin: 0; font-size: 32px; font-family: 'Playfair Display', serif; color: white;">Forum du BTS SIO</h1>

    <div class="buttons" style="display: flex; align-items: center; gap: 12px;">
        <a href="index.php" class="btn">Accueil</a>

        <?php if (isset($_SESSION['prenomMemb'])): ?>
            <?php if (isset($_SESSION['typeMemb']) && $_SESSION['typeMemb'] == 0): ?>
                <a href="admin.php" class="btn">Administration</a>
            <?php endif; ?>

            <p class="user-name" style="margin: 0; color: white;">
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
            <a href="Deconnexion.php" class="btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">Déconnexion</a>
        <?php else: ?>
            <a href="Seconnecter.php" class="btn">Connexion</a>
            <a href="Incription.php" class="btn">Inscription</a>
        <?php endif; ?>
    </div>
</header>


    <div class="container">
        <?php foreach ($categories as $categorie): ?>
            <div class="category">
                <h2><?php echo htmlspecialchars($categorie['nomCat']); ?></h2>
                <div class="rubrics">
                    <?php
                   $queryRubriques = "
                   SELECT 
                       r.idRub, 
                       r.nomRub, 
                       r.descRub, 
                       COUNT(DISTINCT a.idArt) + COUNT(DISTINCT rep.idRep) AS nombre_messages,
                   
                       -- Dernière activité (article ou réponse)
                       (SELECT MAX(dateAction) 
                        FROM (
                            SELECT a.dateArt AS dateAction, a.idRub FROM article a
                            UNION ALL 
                            SELECT rep.dateRep AS dateAction, art.idRub FROM reponse rep
                            JOIN article art ON rep.idArt = art.idArt
                        ) AS all_dates
                        WHERE all_dates.idRub = r.idRub
                       ) AS dernier_message_date,
                   
                       -- Titre du dernier article (même si c'est une réponse, on prend le titre de l'article lié)
                       (SELECT titre 
                        FROM (
                            SELECT a.titreArt AS titre, a.dateArt AS dateAction, a.idRub FROM article a
                            UNION ALL 
                            SELECT art.titreArt AS titre, rep.dateRep AS dateAction, art.idRub FROM reponse rep
                            JOIN article art ON rep.idArt = art.idArt
                        ) AS all_titles
                        WHERE all_titles.idRub = r.idRub
                        ORDER BY dateAction DESC
                        LIMIT 1
                       ) AS dernier_message_titre,
                       
                   
                       -- Auteur de la dernière action
                       (SELECT m.prenomMemb 
                        FROM (
                            SELECT a.idMemb, a.dateArt AS dateAction, a.idRub FROM article a
                            UNION ALL 
                            SELECT rep.idMemb, rep.dateRep AS dateAction, art.idRub FROM reponse rep
                            JOIN article art ON rep.idArt = art.idArt
                        ) AS all_authors
                        JOIN membre m ON all_authors.idMemb = m.idMemb
                        WHERE all_authors.idRub = r.idRub
                        ORDER BY dateAction DESC
                        LIMIT 1
                       ) AS dernier_message_auteur,
                   
                       -- Type de l'auteur
                       (SELECT m.typeMemb 
                        FROM (
                            SELECT a.idMemb, a.dateArt AS dateAction, a.idRub FROM article a
                            UNION ALL 
                            SELECT rep.idMemb, rep.dateRep AS dateAction, art.idRub FROM reponse rep
                            JOIN article art ON rep.idArt = art.idArt
                        ) AS all_authors
                        JOIN membre m ON all_authors.idMemb = m.idMemb
                        WHERE all_authors.idRub = r.idRub
                        ORDER BY dateAction DESC
                        LIMIT 1
                       ) AS auteur_type
                   
                   FROM rubrique r
                   LEFT JOIN article a ON r.idRub = a.idRub
                   LEFT JOIN reponse rep ON a.idArt = rep.idArt
                   WHERE r.idCat = :idCat
                   GROUP BY r.idRub;
                   ";
                   
                
                    $stmtRubriques = $cnn->prepare($queryRubriques);
                    $stmtRubriques->bindParam(':idCat', $categorie['idCat']);
                    $stmtRubriques->execute();
                    $rubriques = $stmtRubriques->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($rubriques as $rubrique): ?>
                        <div class="rubric">
                            <div class="rubric-info">
                                <h3><a href="rubrique.php?idRub=<?php echo $rubrique['idRub']; ?>" class="rubric-link"> <?php echo htmlspecialchars($rubrique['nomRub']); ?> </a></h3>
                                <p><?php echo htmlspecialchars($rubrique['descRub']); ?></p>
                            </div>
                            <div class="rubric-meta">
                                <p><strong><?php echo $rubrique['nombre_messages']; ?></strong> <?php echo ($rubrique['nombre_messages'] <= 1) ? 'message' : 'messages'; ?></p>
                                <?php if ($rubrique['nombre_messages'] > 0): ?>
    <p>
        Par : <strong style="color: <?php
    if ($rubrique['auteur_type'] == 0) {
        echo 'red';
    } elseif ($rubrique['auteur_type'] == 1) {
        echo 'blue';
    } elseif ($rubrique['auteur_type'] == 2) {
        echo 'black';
    }
?>">

            <?php echo htmlspecialchars($rubrique['dernier_message_auteur']); ?>
        </strong>
        <span class="avatar"><?php echo strtoupper(substr($rubrique['dernier_message_auteur'], 0, 1)); ?></span>
        "<?php echo htmlspecialchars($rubrique['dernier_message_titre']); ?>" - 
        <?php echo time_elapsed_string($rubrique['dernier_message_date']); ?>
    </p>
<?php else: ?>
    <p>Aucun</p>
<?php endif; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
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
    <span>🆕</span>
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
