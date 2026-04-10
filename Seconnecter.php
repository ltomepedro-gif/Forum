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
    $mdp = trim($_POST['mot_de_passe']);

    if (empty($email) || empty($mdp)) {
        $errorMessage = "Tous les champs doivent être remplis.";
    } else {
        $checkUser = $cnn->prepare("SELECT * FROM membre WHERE idMemb = :email AND mdpMemb = :mdp");
        $checkUser->execute(['email' => $email, 'mdp' => $mdp]);

        if ($user = $checkUser->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['nomMemb'] = $user['nomMemb'];
            $_SESSION['prenomMemb'] = $user['prenomMemb'];
            $_SESSION['idMemb'] = $user['idMemb'];
            $_SESSION['typeMemb'] = $user['typeMemb'];

            header('Location: index.php');
            exit();
        } else {
            $errorMessage = "Identifiants incorrects.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
        <div class="icon">
            <img src="imagee.png" alt="Login Icon" style="width: 80px; display: block; margin: 0 auto;">
        </div>
        <h2>Connexion</h2>
        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"> <?php echo $errorMessage; ?> </p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <a href="#">Mot de passe oublié ?</a>
<p style="margin-top: 15px; color: white;">
    Pas encore de compte ? 
    <a href="Incription.php" style="color: #4a352a; font-weight: bold; text-decoration: underline;">Veuillez vous inscrire ici</a>
</p>
    </div>
</body>
</html>
