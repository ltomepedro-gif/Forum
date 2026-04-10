<?php
require_once 'db.php';

// Récupération des thèmes avec leurs formations
$themes = $pdo->query(
    "SELECT * FROM theme ORDER BY NomTheme"
)->fetchAll();

$allFormations = $pdo->query(
    "SELECT f.*, t.NomTheme
     FROM formation f
     LEFT JOIN theme t ON f.IdTheme = t.IdTheme
     ORDER BY t.NomTheme, f.Descriptif"
)->fetchAll();

// Grouper par thème
$byTheme = [];
foreach ($allFormations as $f) {
    $byTheme[$f['NomTheme'] ?? 'Autre'][] = $f;
}

// Icônes par thème
$themeIcons = [
    'Bureautique'   => '📝',
    'Système'       => '🐧',
    'Programmation' => '🐍',
    'Présentation'  => '🖥️',
    'Autre'         => '📚',
];
$niveauColors = [
    'Débutant'      => ['bg'=>'#D6F5E0','color'=>'#217346'],
    'Intermédiaire' => ['bg'=>'#F0DDD0','color'=>'#9E5B25'],
    'Avancé'        => ['bg'=>'#EDE0F5','color'=>'#6a3d9a'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formations — FormaPro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #F5F2EE;
      --surface: #FFFFFF;
      --ink: #1A1714;
      --ink-soft: #5C5650;
      --accent: #C4753A;
      --accent-light: #F0DDD0;
      --accent-dark: #9E5B25;
      --line: #E0DAD4;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--ink); line-height: 1.6; overflow-x: hidden; }

    /* NAV */
    nav { position:fixed; top:0; left:0; right:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:0 5%; height:72px; background:rgba(245,242,238,0.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--line); }
    .nav-logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.45rem; letter-spacing:-.02em; color:var(--ink); text-decoration:none; }
    .nav-logo span { color: var(--accent); }
    .nav-links { display:flex; gap:2.2rem; list-style:none; }
    .nav-links a { font-size:.88rem; font-weight:500; color:var(--ink-soft); text-decoration:none; transition:color .2s; }
    .nav-links a:hover, .nav-links a.active { color:var(--accent); }
    .nav-cta { background:var(--ink)!important; color:#fff!important; padding:.45rem 1.2rem; border-radius:50px; }
    .nav-cta:hover { background:var(--accent)!important; }

    /* PAGE HEADER */
    .page-header {
      padding: 130px 5% 60px;
      background: var(--surface);
      border-bottom: 1px solid var(--line);
      position: relative;
      overflow: hidden;
    }
    .page-header::after {
      content: '';
      position: absolute;
      top: -60px; right: -80px;
      width: 400px; height: 400px;
      background: radial-gradient(circle, var(--accent-light) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .page-header-content { position: relative; z-index: 1; }
    .page-header .eyebrow { display:inline-block; font-size:.75rem; font-weight:500; letter-spacing:.13em; text-transform:uppercase; color:var(--accent); margin-bottom:.7rem; }
    .page-header h1 { font-family:'Syne',sans-serif; font-weight:800; font-size:clamp(2.2rem,4vw,3.5rem); line-height:1.1; letter-spacing:-.03em; margin-bottom:.8rem; }
    .page-header p { font-size:1rem; color:var(--ink-soft); font-weight:300; max-width:500px; }

    /* FILTER BAR */
    .filter-bar {
      background: var(--surface);
      border-bottom: 1px solid var(--line);
      padding: 1rem 5%;
      display: flex; gap: .7rem; flex-wrap: wrap; align-items: center;
      position: sticky; top: 72px; z-index: 90;
    }
    .filter-label { font-size:.78rem; font-weight:500; color:var(--ink-soft); letter-spacing:.06em; text-transform:uppercase; margin-right:.3rem; }
    .filter-btn {
      font-family:'DM Sans',sans-serif; font-size:.82rem; font-weight:500;
      padding:.35rem .9rem; border-radius:50px;
      border: 1.5px solid var(--line);
      background: transparent; color: var(--ink-soft);
      cursor: pointer; transition: all .2s;
    }
    .filter-btn:hover, .filter-btn.active { background:var(--accent); border-color:var(--accent); color:#fff; }

    /* CONTENT */
    main { padding: 60px 5%; }

    /* THEME SECTION */
    .theme-section { margin-bottom: 3.5rem; }
    .theme-heading {
      display: flex; align-items: center; gap: .9rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--line);
    }
    .theme-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--accent-light); display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
    .theme-heading h2 { font-family:'Syne',sans-serif; font-weight:700; font-size:1.3rem; }
    .theme-count { font-size:.78rem; color:var(--ink-soft); background:var(--bg); padding:.25rem .7rem; border-radius:50px; border:1px solid var(--line); }

    /* FORMATION CARDS */
    .formations-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.3rem; }
    .card {
      background: var(--surface);
      border-radius: 16px;
      border: 1px solid var(--line);
      padding: 1.8rem;
      display: flex; flex-direction: column;
      transition: transform .2s, box-shadow .2s;
      position: relative; overflow: hidden;
    }
    .card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent); transform:scaleX(0); transform-origin:left; transition:transform .3s; }
    .card:hover { transform:translateY(-4px); box-shadow:0 12px 40px rgba(0,0,0,.08); }
    .card:hover::before { transform:scaleX(1); }
    .card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; }
    .card-id { font-size:.7rem; color:var(--ink-soft); font-weight:500; opacity:.6; }
    .niveau-badge { display:inline-block; font-size:.68rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; padding:.22rem .65rem; border-radius:50px; }
    .card h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:1.05rem; margin-bottom:.5rem; line-height:1.3; }
    .card-desc { font-size:.86rem; color:var(--ink-soft); font-weight:300; flex:1; margin-bottom:1.2rem; }
    .card-footer { display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--line); padding-top:1rem; margin-top:auto; }
    .card-meta span { display:block; font-size:.76rem; color:var(--ink-soft); }
    .card-meta strong { font-size:.86rem; }
    .pdf-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.78rem; font-weight:500; color:var(--accent); text-decoration:none; padding:.3rem .8rem; background:var(--accent-light); border-radius:50px; transition:background .2s; }
    .pdf-link:hover { background:#e8c9b0; }
    .btn-inscrire { display:inline-flex; align-items:center; gap:.3rem; font-size:.82rem; font-weight:500; color:var(--accent); text-decoration:none; transition:gap .2s; }
    .btn-inscrire:hover { gap:.6rem; }

    /* EMPTY STATE */
    .empty-state { text-align:center; padding:4rem 2rem; color:var(--ink-soft); }
    .empty-state p { font-size:1rem; }

    /* BUTTON */
    .btn-outline { display:inline-flex; align-items:center; gap:.5rem; background:transparent; color:var(--ink); font-family:'DM Sans',sans-serif; font-size:.92rem; font-weight:500; padding:.85rem 2rem; border-radius:50px; text-decoration:none; border:1.5px solid var(--line); cursor:pointer; transition:border-color .2s,transform .15s; }
    .btn-outline:hover { border-color:var(--ink); transform:translateY(-1px); }

    /* FOOTER */
    footer { background:var(--ink); color:rgba(255,255,255,.65); padding:2.8rem 5%; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .footer-logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.3rem; color:#fff; }
    .footer-logo span { color:var(--accent); }
    footer p { font-size:.8rem; font-weight:300; }
    .footer-links { display:flex; gap:1.5rem; list-style:none; }
    .footer-links a { font-size:.8rem; color:rgba(255,255,255,.5); text-decoration:none; transition:color .2s; }
    .footer-links a:hover { color:var(--accent); }

    /* REVEAL */
    .reveal { opacity:0; transform:translateY(20px); transition:opacity .5s,transform .5s; }
    .reveal.visible { opacity:1; transform:none; }

    @media(max-width:600px){ .nav-links{display:none;} .formations-grid{grid-template-columns:1fr;} }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.php" class="nav-logo">Forma<span>Pro</span></a>
  <ul class="nav-links">
    <li><a href="formations.php" class="active">Formations</a></li>
    <li><a href="index.php#apropos">À propos</a></li>
    <li><a href="index.php#contact" class="nav-cta">S'inscrire</a></li>
  </ul>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-content">
    <span class="eyebrow">Catalogue complet</span>
    <h1>Nos formations</h1>
    <p><?= count($allFormations) ?> formation<?= count($allFormations) > 1 ? 's' : '' ?> disponible<?= count($allFormations) > 1 ? 's' : '' ?> — organisées par thème</p>
  </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
  <span class="filter-label">Filtrer :</span>
  <button class="filter-btn active" data-filter="all">Toutes</button>
  <?php foreach (array_keys($byTheme) as $th): ?>
    <button class="filter-btn" data-filter="<?= htmlspecialchars($th) ?>"><?= htmlspecialchars($th) ?></button>
  <?php endforeach; ?>
</div>

<!-- MAIN CONTENT -->
<main>
  <?php if (empty($byTheme)): ?>
    <div class="empty-state">
      <p>Aucune formation disponible pour le moment.</p>
    </div>
  <?php else: ?>
    <?php foreach ($byTheme as $theme => $formations): ?>
    <div class="theme-section reveal" data-theme="<?= htmlspecialchars($theme) ?>">
      <div class="theme-heading">
        <div class="theme-icon"><?= $themeIcons[$theme] ?? '📚' ?></div>
        <h2><?= htmlspecialchars($theme) ?></h2>
        <span class="theme-count"><?= count($formations) ?> formation<?= count($formations) > 1 ? 's' : '' ?></span>
      </div>

      <div class="formations-grid">
        <?php foreach ($formations as $f): 
          $nc = $niveauColors[$f['Niveau'] ?? ''] ?? ['bg'=>'#E0DAD4','color'=>'#5C5650'];
        ?>
        <div class="card reveal">
          <div class="card-top">
            <span class="card-id">#FP-<?= str_pad($f['IdFormation'], 3, '0', STR_PAD_LEFT) ?></span>
            <?php if ($f['Niveau']): ?>
              <span class="niveau-badge" style="background:<?= $nc['bg'] ?>;color:<?= $nc['color'] ?>"><?= htmlspecialchars($f['Niveau']) ?></span>
            <?php endif; ?>
          </div>
          <h3><?= htmlspecialchars($f['Descriptif']) ?></h3>
          <p class="card-desc">
            Thème : <?= htmlspecialchars($theme) ?>
            <?php if ($f['NomFichier']): ?>
              &nbsp;·&nbsp; <a href="<?= htmlspecialchars($f['NomFichier']) ?>" class="pdf-link" download>📄 Plaquette</a>
            <?php endif; ?>
          </p>
          <div class="card-footer">
            <div class="card-meta">
              <span>Capacité maximale</span>
              <strong>
                <?= ($f['Niveau'] === 'Débutant') ? '20' : '30' ?> participants
              </strong>
            </div>
            <a href="index.php#contact" class="btn-inscrire">S'inscrire →</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div style="text-align:center;margin-top:2rem">
    <a href="index.php#contact" class="btn-outline">Vous ne trouvez pas ce que vous cherchez ? Contactez-nous →</a>
  </div>
</main>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">Forma<span>Pro</span></div>
  <p>© <?= date('Y') ?> FormaPro. Tous droits réservés.</p>
  <ul class="footer-links">
    <li><a href="#">Mentions légales</a></li>
    <li><a href="#">RGPD</a></li>
    <li><a href="index.php#contact">Contact</a></li>
  </ul>
</footer>

<script>
  // Filter logic
  const filterBtns = document.querySelectorAll('.filter-btn');
  const sections   = document.querySelectorAll('.theme-section');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const f = btn.dataset.filter;
      sections.forEach(s => {
        s.style.display = (f === 'all' || s.dataset.theme === f) ? '' : 'none';
      });
    });
  });

  // Reveal on scroll
  const reveals = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e,i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), i * 80);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  reveals.forEach(el => obs.observe(el));
</script>
</body>
</html>
