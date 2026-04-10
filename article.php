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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_reponse']) && $_SESSION['typeMemb'] == 1) {
    $idRep = intval($_POST['idRep']);
    $cnn->prepare("DELETE FROM reponse WHERE idRep = ?")->execute([$idRep]);
    header("Location: article.php?idArt=" . $_GET['idArt'] . "&idRub=" . $_GET['idRub']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'], $_POST['idArt']) && isset($_SESSION['idMemb'])) {
    $idMemb = $_SESSION['idMemb'];
    $contenu = $_POST['contenu'];
    $idArtPost = $_POST['idArt'];
    $date = date('Y-m-d H:i:s');

    $insert = $cnn->prepare("INSERT INTO reponse (idMemb, idArt, dateRep, contenuRep) VALUES (:idMemb, :idArt, :dateRep, :contenuRep)");
    $insert->execute([
        'idMemb' => $idMemb,
        'idArt' => $idArtPost,
        'dateRep' => $date,
        'contenuRep' => $contenu
    ]);

    header("Location: article.php?idArt=$idArtPost&idRub=$idRub");
    exit();
}

date_default_timezone_set('UTC');

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 1) return 'hier';
    elseif ($diff->d == 2) return 'avant-hier';
    elseif ($diff->d > 2 && $diff->d < 7) return 'il y a ' . $diff->d . ' jours';
    elseif ($diff->d >= 7) return 'le ' . $ago->format('d F');
    elseif ($diff->h > 0) return 'il y a ' . $diff->h . ' ' . ($diff->h == 1 ? 'heure' : 'heures');
    elseif ($diff->i > 0) return 'il y a ' . $diff->i . ' ' . ($diff->i == 1 ? 'minute' : 'minutes');
    else return 'à l’instant';
}

if (!isset($_GET['idArt']) || !isset($_GET['idRub'])) {
    echo "Article introuvable.";
    exit();
}

$idArt = $_GET['idArt'];
$idRub = $_GET['idRub'];

$queryArticle = "
SELECT a.*, m.nomMemb, m.prenomMemb, m.typeMemb, r.nomRub, r.idCat, c.nomCat
FROM article a
JOIN membre m ON a.idMemb = m.idMemb
JOIN rubrique r ON a.idRub = r.idRub
JOIN categorie c ON r.idCat = c.idCat
WHERE a.idArt = :idArt
";
$stmt = $cnn->prepare($queryArticle);
$stmt->bindParam(':idArt', $idArt);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "Article non trouvé.";
    exit();
}

$queryRep = "
SELECT r.*, m.prenomMemb, m.typeMemb
FROM reponse r
JOIN membre m ON r.idMemb = m.idMemb
WHERE r.idArt = :idArt
ORDER BY r.dateRep ASC
";

$stmtRep = $cnn->prepare($queryRep);
$stmtRep->bindParam(':idArt', $idArt);
$stmtRep->execute();
$reponses = $stmtRep->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article['titreArt']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FFEDDE; margin: 0; padding: 0; }
        header { background-color: #BEA692; padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #fff; }
        .container { max-width: 1000px; margin: 30px auto; background: #BEA692; padding: 20px; border-radius: 8px; color: white; }
        .article-title { font-size: 36px; font-family: 'Playfair Display', serif; text-align: center; margin-bottom: 20px; letter-spacing: 1px; }
        .meta { font-size: 14px; color: #f0e6e6; margin-bottom: 15px; }
        .content { background: white; color: black; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .reponse-form textarea { width: 100%; height: 100px; border-radius: 5px; padding: 10px; }
        .reponse-form button, .reponse-form a { background-color: #4a352a; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; }
        .reponse-form .button-group { display: flex; gap: 10px; margin-top: 10px; }
        .reponse-box { background: white; color: black; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
        .reponse-meta { font-size: 12px; color: #555; margin-top: 8px; }
        .buttons a { color: white; text-decoration: none; padding: 8px 16px; border: 1px solid white; border-radius: 5px; transition: 0.3s; }
        .buttons a:hover { background-color: white; color: #4a352a; }
        .buttons { display: flex; gap: 10px; }
        footer { text-align: center; padding: 1px; font-size: 14px; background: #4a352a; }
        .avatar { display: inline-block; width: 30px; height: 30px; background-color: #5A5858; color: white; font-weight: bold; font-size: 16px; text-align: center; line-height: 30px; border-radius: 50%; margin-right: 8px; vertical-align: middle; }
        .stats { text-align: center; background: #BEA692; padding: 1px 10px; border-top: 2px solid white; display: flex; flex-direction: column; align-items: center; }
        .stats-container { display: flex; justify-content: space-between; width: 80%; max-width: 1000px; }
        .stats-item { display: flex; flex-direction: column; align-items: center; font-size: 18px; }
        .stats-item span { font-size: 50px; }
        .stats-item p { margin: 5px 0 0; font-size: 16px; font-weight: bold; }
        .last-member-info { display: flex; flex-direction: column; align-items: center; text-align: center; font-size: 18px; }
        .name-avatar { display: flex; align-items: center; gap: 1px; justify-content: center; }
        .last-member .avatar { width: 30px; height: 30px; font-size: 16px; line-height: 30px; border-radius: 50%; text-align: center; background-color: #5A5858; color: white; display: flex; justify-content: center; align-items: center; font-weight: bold; }
        .last-member .date { font-size: 12px; color: #444; margin-top: 5px; text-align: center; }
    </style>
</head>
<body>
<header style="background-color: #BEA692; position: relative; padding: 20px;">
    <h1 style="
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: bold;
        color: white;
        text-align: center;
        margin: 0;
    ">
        <?php echo htmlspecialchars($article['nomRub']); ?>
    </h1>

    <div class="buttons" style="position: absolute; top: 20px; right: 20px; display: flex; gap: 10px; align-items: center;">
        <?php if (isset($_SESSION['prenomMemb'])): ?>
            <?php if (isset($_SESSION['typeMemb']) && $_SESSION['typeMemb'] == 0): ?>
                <a href="admin.php" class="btn" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Administration</a>
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
                </strong>!
            </p>
            <a href="Deconnexion.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Déconnexion</a>
        <?php else: ?>
            <a href="Seconnecter.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Connexion</a>
            <a href="Incription.php" style="color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;">Inscription</a>
        <?php endif; ?>
    </div>
</header>


<div class="container">
    <h1 class="article-title"><?php echo htmlspecialchars($article['titreArt']); ?></h1>
    <div class="meta">
    Posté par 
                <strong style="color: <?php 
                    if ($article['typeMemb'] == 0) {
                        echo 'red';
                    } elseif ($article['typeMemb'] == 1) {
                        echo 'blue';
                    } elseif ($article['typeMemb'] == 2) {
                        echo 'black';
                    }
                ?>">
            <?php echo htmlspecialchars($article['prenomMemb']); ?>
        </strong>
        <span class="avatar"><?php echo strtoupper(substr($article['prenomMemb'], 0, 1)); ?></span> 
        - <?php echo time_elapsed_string($article['dateArt']); ?>
    </div>
    <div class="content"><?php echo nl2br(htmlspecialchars($article['contenuArt'])); ?></div>

    <h3>Réponses :</h3>
    <?php if (empty($reponses)): ?>
        <p>Aucune réponse pour le moment.</p>
    <?php else: ?>
        <?php foreach ($reponses as $rep): ?>
            <div class="reponse-box">
                <?php echo nl2br(htmlspecialchars($rep['contenuRep'])); ?>
                <div class="reponse-meta">
                Posté par 
                <strong style="color: <?php 
                    if ($rep['typeMemb'] == 0) {
                        echo 'red';
                    } elseif ($rep['typeMemb'] == 1) {
                        echo 'blue';
                    } elseif ($rep['typeMemb'] == 2) {
                        echo 'black';
                    }
                ?>">
                        <?php echo htmlspecialchars($rep['prenomMemb']); ?>
                    </strong>
                    <span class="avatar"><?php echo strtoupper(substr($rep['prenomMemb'], 0, 1)); ?></span>
                    - <?php echo time_elapsed_string($rep['dateRep']); ?>
                    <?php if (isset($_SESSION['typeMemb']) && $_SESSION['typeMemb'] == 1): ?>
    <form method="POST" onsubmit="return confirm('Supprimer ce commentaire ?');" style="display: inline;">
        <input type="hidden" name="idRep" value="<?php echo $rep['idRep']; ?>">
        <button type="submit" name="supprimer_reponse" style="background-color: crimson; color: white; border: none; padding: 4px 8px; border-radius: 4px; margin-left: 10px;">Supprimer</button>
    </form>
<?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="reponse-form">
        <h4>Répondre :</h4>
        <?php if (isset($_SESSION['idMemb'])): ?>
            <form method="POST" action="article.php?idArt=<?php echo $idArt; ?>&idRub=<?php echo $idRub; ?>">
                <textarea name="contenu" id="contenu" required placeholder="Écrire une réponse..."></textarea>
                <input type="hidden" name="idArt" value="<?php echo $idArt; ?>">
                <div class="button-group">
<button type="submit" class="btn-marron">Répondre</button>
                    <a href="index.php">Retour à l'accueil</a>
                </div>
            </form>
        <?php else: ?>
            <p style="color: black; font-weight: bold;">
    Veuillez vous <a href="Seconnecter.php" style="background-color: #4a352a; color: white;padding: 4px 10px;border-radius: 5px;text-decoration: none;font-weight: bold;">connecter</a>
 pour répondre à cet article.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php
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
?>

<div class="stats">
    <h3>Statistiques du Forum</h3>
    <div class="stats-container">
        <div class="stats-item"><span>📄</span><p>Total des articles : <strong><?php echo $stats['total_articles']; ?></strong></p></div>
        <div class="stats-item"><span>💬</span><p>Total des messages : <strong><?php echo $stats['total_messages']; ?></strong></p></div>
        <div class="stats-item"><span>👥</span><p>Total des membres : <strong><?php echo $stats['total_membres']; ?></strong></p></div>
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

<footer>&copy; 2025 Lycée Mathias - Forum BTS SIO</footer>
</body>
</html>
