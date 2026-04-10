<?php
session_start();

$host = 'localhost';
$bdd = 'forum';
$user = 'root';
$passwd = 'root';

try {
    $cnn = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $user, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']); 
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $mdp = trim($_POST['mot_de_passe']);
    $confirm_mdp = trim($_POST['confirm_mot_de_passe']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($confirm_mdp)) {
        $errorMessage = "Tous les champs doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "L'adresse email n'est pas valide.";
    } elseif ($mdp !== $confirm_mdp) {
        $errorMessage = "Les mots de passe ne correspondent pas.";
    } else {
        $checkUser = $cnn->prepare("SELECT * FROM membre WHERE idMemb = :email");
        $checkUser->execute(['email' => $email]);

        if ($checkUser->rowCount() > 0) {
            $errorMessage = "Cet email est déjà utilisé.";
        } else {
            $dateIns = date('Y-m-d H:i:s');

            $stmt = $cnn->prepare("INSERT INTO membre (idMemb, nomMemb, prenomMemb, dateIns, mdpMemb, typeMemb) VALUES (:email, :nom, :prenom, :dateIns, :mdp, 2)");
            $stmt->execute([
                'email' => $email,
                'nom' => $nom,
                'prenom' => $prenom,
                'dateIns' => $dateIns,
                'mdp' => $mdp
            ]);

            $_SESSION['idMemb'] = $email; 
            $_SESSION['nomMemb'] = $nom;
            $_SESSION['prenomMemb'] = $prenom;
            $_SESSION['typeMemb'] = 2;

            header('Location: index.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #FFEDDE;
            color: #FFFFFF;
        }

        .login-container {
            display: flex;
            align-items: center;
            background: #BEA692;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px 50px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            flex-direction: column;
        }

        .login-container h2 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .login-container input {
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
            background: #fff;
            color: #333;
        }

        .login-container button {
            padding: 12px;
            font-size: 16px;
            color: #FFFFFF;
            background-color: #4a352a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            font-weight: bold;
        }

        .login-container button:hover {
            background-color: #fff;
            color: #4a352a;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <a href="index.php" style="position: absolute; top: 20px; left: 20px; text-decoration: none; color: white; font-size: 16px; font-weight: bold; background-color: #4a352a; border-radius: 5px; padding: 8px 12px; transition: 0.3s;">← Retour</a>
    <div class="login-container">
        <h2>Inscription</h2>
        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"> <?php echo $errorMessage; ?> </p>
        <?php endif; ?>
        <form action="" method="POST">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer le mot de passe" required>

    <div style="margin-bottom: 10px;">
        <input type="checkbox" id="accept-cgu" />
        <label for="accept-cgu">
            J’ai lu et j’accepte les <a href="javascript:void(0);" id="show-conditions">Conditions Générales d'Utilisation</a>
        </label>
    </div>
    <p id="checkbox-error" class="error-message" style="display: none;"></p>
    <button type="submit" id="submit-btn">S'inscrire</button>
</form>

</div>
<!-- Overlay des conditions -->
<div id="conditions-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.9); z-index: 9999; overflow-y: auto; color: white; padding: 40px; box-sizing: border-box;">
    
    <div style="max-width: 800px; margin: auto;">
        <h2 style="margin-top: 0;">Conditions d'utilisation générales</h2>
        <h2>Responsabilité de l'utilisateur</h2>

            <h3>Sécurité de son compte</h3>
            <p>A la création d'un forum ou à l'inscription sur un forum, l'Utilisateur est amené à choisir un nom d'utilisateur et un mot de passe. L'Utilisateur est seul responsable de la confidentialité de ses identifiants, et le demeure en cas d'actions non autorisées effectuées par un tiers grâce à ceux-ci. Il est conseillé, à ce titre, de mettre fin à la session (déconnexion) à l'issue de l'utilisation des services. En cas d'utilisation frauduleuse de ses identifiants, l'Utilisateur a l'obligation d'informer sans délai forumactif.com en indiquant les violations ayant pu être commises.</p>

            <h3>Utilisation des services</h3>
            <p>Les contenus publiés par le biais des services, de manière publique ou non, et quelle que soit leur nature (notamment, mais non exclusivement : information, code, donnée, texte, logiciel, musique, son, photographie, image, graphique, vidéo, chat, messages, dossiers) engagent la responsabilité de l'Utilisateur à l'origine de la publication. L'Utilisateur déclare être titulaire de tous les droits et autorisations nécessaires à la diffusion de ces contenus, et s'engage à ne pas publier de contenus contraires aux présentes Conditions. En aucun cas forumactif.com ne peut être tenu pour responsable des conséquences d'une telle publication, ou des pertes ou dommages en résultant.</p>
            <p>Toutes informations, codes, données, textes, logiciels, musiques, sons, photographies, images, graphiques, vidéos, chats, messages, dossiers, ou contenus d'autres natures publiés par le biais des services n'engagent que la responsabilité de l'Utilisateur à l'origine de la publication, que celle-ci ait eu lieu publiquement ou non. En aucun cas forumactif.com ne peut être tenu pour responsable des erreurs, inexactitudes ou omissions au sein d'un contenu publié, ou des pertes et dommages nés ou pouvant naitre de tels contenus.</p>
            
            <h3>Dommages causés</h3>
            <p>L'Utilisateur est responsable des dommages de toute nature, matériels ou immatériels, directs ou indirects, causés à tout tiers, ainsi qu'à forumactif.com du fait de l'utilisation ou de l'exploitation illicite des services, quels que soient la cause et le lieu de survenance de ces dommages et garantit forumactif.com des conséquences des réclamations ou actions dont elle pourrait faire l'objet. L'Utilisateur renonce en outre à exercer tout recours contre forumactif.com dans le cas de poursuites diligentées par un tiers à son encontre du fait de l'utilisation et/ou de l'exploitation illicite des services.</p>

            <h3>Obligations de tout utilisateur</h3>
            <p>L'Utilisateur s'engage à faire des services un usage conforme au but pour lequel ils ont été conçus, et en tout point conforme aux présentes Conditions. A cet égard, il est rappelé que, conformément aux dispositions précédentes, l'Utilisateur est seul responsable des contenus qu'il publie.</p>
            <p>L'Utilisateur s'engage à ne pas participer à toute action ayant pour objet ou pour effet d'attenter au bon fonctionnement des services, notamment mais non exclusivement par (I) tous comportements de nature à interrompre, suspendre, ralentir ou empêcher la continuité des Services, (II) toutes intrusions ou tentatives d'intrusions dans les systèmes de forumactif.com, (III) tous détournements des ressources système du site, (IV) toutes actions de nature à imposer une charge disproportionnée sur les infrastructures de cette dernière, (V) toutes atteintes aux mesures de sécurité et d'authentification, (VI) tous actes de nature à porter atteinte aux droits et intérêts financiers, commerciaux ou moraux de forumactif.com ou des utilisateurs de son site.<p>
            <p>En utilisant les services proposés par forumactif.com, l'Utilisateur accepte que les Administrateurs du forum soient seuls en charge de la gestion de celui-ci, et admet notamment que ceux-ci modère les contenus postés, et gère les Membres.</p>
            <p>L'Utilisateur s'engage à respecter les droits de propriété intellectuelle des tiers, et de forumactif.com. L'ensemble des éléments visibles sur le site est protégé par la législation sur le droit d'auteur. Il ne peut en aucun cas utiliser, distribuer, copier, reproduire, modifier, dénaturer ou transmettre tout ou partie du site ou de ses éléments, tels que textes, images, vidéos, sans l'autorisation écrite et préalable de la société. Les marques et logos figurant sur le site, sont la propriété de la société ou font l'objet d'une autorisation d'utilisation. Aucun droit ou licence ne saurait être attribué sur l'un quelconque de ces éléments sans l'autorisation écrite de la société ou du tiers, détenteur des droits sur la marque ou logo figurant sur le site. La société se réserve le droit de poursuivre tout acte de contrefaçon de ses droits de propriété intellectuelle, y compris dans le cadre d'une action pénale.<p>

            <h2>Comportements et contenus prohibés</h2>
            <p>En utilisant les services proposés par forumactif.com, l'Utilisateur s'engage à en faire un usage conforme au but pour lequel ils ont été conçus, et à ne pas utiliser les produits et services afin – notamment – d'inciter, de favoriser, d'accueillir ou de présenter sous un jour favorable :</p>
            <ul>
                <li>Le piratage, hacking, spamming, et attaques contre des réseaux et/ou serveurs, le phishing, le malware, l'intrusion dans le réseau de tiers,</li>
                <li>Les contenus à caractère sexuel, obscène, pornographique,</li>
                <li>Les contenus violents, diffamants, discriminants, incitants à la haine raciale, les crimes contre l'humanité,</li>
                <li>Le partage, l'hébergement, la diffusion ou le piratage d'œuvre et contenus protégés par le droit d'auteur et la propriété intellectuelle, ou toute pratique contrefaisante,</li>
                <li>La vente, l'échange ou le don de produits soumis à législation spéciale, de médicaments soumis ou non à prescription médicale, de produits stupéfiants et autres substances illicites,</li>
                <li>La fraude à la carte bancaire, ou les pratiques trompeuses.</li>
                <li>Les atteintes aux droits et aux intérêts des mineurs,</li>
                <li>Tout comportement contraire aux lois en vigueur, portant atteinte aux droits des tiers, ou préjudiciable à ceux-ci.</li>
            </ul>
            <p>forumactif.com est un service gratuit mettant tout en œuvre pour offrir un service de qualité à la pointe de la technologie. A l'exception de ce qui est rendu possible par la gestion des crédits, il est formellement interdit de supprimer, de masquer, ou de rendre illisible par quelque moyen que ce soit les mentions obligatoires et copyrights figurant sur les Forums (notamment dans la barre d'outil et le pied de page du forum), ainsi que les contenus sponsorisés ou les publicités. Ces éléments peuvent être retirés par le biais de la gestion des crédits uniquement.</p>
            <p>forumactif.com se réserve le droit de supprimer les forums, messages ou utilisateurs faisant un usage des services manifestement illégal ou contraire aux présentes Conditions, sans préavis. A ce titre, les forums contenant des textes, liens, images, animations, vidéos, petites annonces ou contenus d'une autre nature considérés comme contraires aux présentes Conditions sont susceptibles d'être supprimés sans préavis.</p>

            <h2>Données personnelles et cookies</h2>

            <h3>Utilisation des données personnelles</h3>
            <p>Le traitement de vos données personnelles repose sur votre consentement, hors les cas où les données sont nécessaires à l'exécution d'un contrat, ou au respect d'une obligation légale. Dans les cas où ce traitement repose sur le consentement de l'Utilisateur, il peut retirer son consentement à tout moment afin de le faire cesser selon les modalités prévues dans notre Politique de confidentialité. Le retrait du consentement ne compromet pas la licéité du traitement fondé sur le consentement effectué avant ce retrait.</p>
            <p>Toutes les informations que nous recueillons sont utiles au bon fonctionnement du service, et permettent notamment de personnaliser votre expérience d'utilisateur, d'améliorer l'affichage et le fonctionnement des pages, et, le cas échéant, fournir des résultats publicitaires personnalisés. Certaines de ces données sont susceptibles d'être utilisés si l'Utilisateur y a consenti, notamment s'agissant de la réception de messages électroniques d'information (newsletters) de la part des forums sur lesquels il s'est inscrit.</p>
            <p>L'Utilisateur bénéficie d'un droit d'accès, de rectification et de suppression des données personnelles le concernant, ainsi que du droit d'introduire une réclamation auprès d'une autorité de contrôle. Les modalités de l'exercice de ces droits sont détaillées dans notre Politique de confidentialité.</p>
            <p>L'utilisation des services proposés par forumactif.com est subordonnée à la lecture et à l'acceptation par l'Utilisateur de la Politique de confidentialité.</p>

            <h3>Utilisation des cookies</h3>
            <p>Un cookie est un fichier texte contenant un nombre limité d'informations, qui est téléchargé sur l'appareil de l'utilisateur lorsqu'il visite un site internet. Il permet ainsi au site d'identifier l'utilisateur et de mémoriser certaines informations sur sa navigation, ou de lui offrir des services additionnels, comme la gestion des sessions, ou des publicités.</p>
            <p>forumactif.com se réserve le droit d'utiliser des cookies pour améliorer l'expérience utilisateur, personnaliser la publicité, et analyser les tendances de l'utilisation des services. Ces cookies peuvent être désactivés par l'utilisateur dans les paramètres de son navigateur, mais cela pourrait nuire à certaines fonctionnalités du site.</p>


        <button id="close-conditions" style="margin-top: 30px; background-color: #ff4d4d; color: white;
            border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            Fermer
        </button>
    </div>
</div>

</body>
</html>
<script>
    const showBtn = document.getElementById("show-conditions");
    const closeBtn = document.getElementById("close-conditions");
    const overlay = document.getElementById("conditions-overlay");

    const checkbox = document.getElementById("accept-cgu");
    const form = document.querySelector("form");
    const submitBtn = document.getElementById("submit-btn");

    // Crée dynamiquement une zone pour les messages d’erreur
    const errorBox = document.createElement("p");
    errorBox.id = "checkbox-error";
    errorBox.className = "error-message";
    errorBox.style.color = "red";
    errorBox.style.marginBottom = "10px";
    errorBox.style.display = "none";
    submitBtn.parentNode.insertBefore(errorBox, submitBtn);

    showBtn.addEventListener("click", function () {
        overlay.style.display = "block";
        document.body.style.overflow = "hidden";
    });

    closeBtn.addEventListener("click", function () {
        overlay.style.display = "none";
        document.body.style.overflow = "auto";
    });

    form.addEventListener("submit", function (e) {
        if (!checkbox.checked) {
            e.preventDefault();
            errorBox.textContent = "Veuillez accepter les conditions pour vous inscrire.";
            errorBox.style.display = "block";
        } else {
            errorBox.style.display = "none";
        }
    });
</script>

        </form>
    </div>
</body>
</html>
