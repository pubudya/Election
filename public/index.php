<?php 
$activePage = 'home'; 
include 'header.php'; 
?>
    <main class="container">
    <!-- Security Messages Section -->
    <?php if (isset($_GET['error']) || isset($_GET['success']) || isset($_GET['warning'])): ?>
        <div class="alert-container mb-4">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border: 2px solid #dc3545; border-radius: 12px; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);">
                    <div class="d-flex align-items-center">
                        <span style="font-size: 1.5rem; margin-right: 12px;">⚠️</span>
                        <div class="flex-grow-1">
                            <strong style="color: #721c24;">Security Notice:</strong><br>
                            <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($_GET['error']); ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border: 2px solid #28a745; border-radius: 12px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);">
                    <div class="d-flex align-items-center">
                        <span style="font-size: 1.5rem; margin-right: 12px;">✅</span>
                        <div class="flex-grow-1">
                            <strong style="color: #155724;">Success:</strong><br>
                            <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($_GET['success']); ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['warning'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border: 2px solid #ffc107; border-radius: 12px; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);">
                    <div class="d-flex align-items-center">
                        <span style="font-size: 1.5rem; margin-right: 12px;">⚠️</span>
                        <div class="flex-grow-1">
                            <strong style="color: #856404;">Warning:</strong><br>
                            <span style="font-size: 1.1rem;"><?php echo htmlspecialchars($_GET['warning']); ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    

    

    
    <div class="main-card" id="home-links">
        <h2 class="section-title"><?= $tr['presidential_election'] ?></h2>
        <p><strong><?= $tr['expected_date'] ?>:</strong> <?= $tr['october_2024'] ?></p>
        <hr>
        <!-- BEGIN: Election Info Quick Links -->
        <div class="election-links-grid" style="display: flex; flex-wrap: wrap; gap: 24px; justify-content: center; margin: 32px 0 40px 0;">
            <a href="why-vote.php" class="election-link-card" style="flex: 1 1 220px; max-width: 260px; min-width: 200px; background: var(--sl-cream); border-radius: 18px; box-shadow: var(--sl-shadow); text-decoration: none; color: var(--sl-maroon-dark); padding: 28px 18px 22px 18px; display: flex; flex-direction: column; align-items: center; transition: transform 0.18s, box-shadow 0.18s; border: 2.5px solid var(--sl-gold);">
                <span style="font-size: 2.2rem; margin-bottom: 10px;">🗳️</span>
                <span style="font-size: 1.2rem; font-weight: bold; margin-bottom: 6px;"><?= $tr['why_vote_title'] ?></span>
                <span style="font-size: 0.98rem; color: #444; text-align: center;"><?= $tr['why_vote_description'] ?></span>
            </a>
            <a href="how-election-works.php" class="election-link-card" style="flex: 1 1 220px; max-width: 260px; min-width: 200px; background: var(--sl-cream); border-radius: 18px; box-shadow: var(--sl-shadow); text-decoration: none; color: var(--sl-maroon-dark); padding: 28px 18px 22px 18px; display: flex; flex-direction: column; align-items: center; transition: transform 0.18s, box-shadow 0.18s; border: 2.5px solid var(--sl-gold);">
                <span style="font-size: 2.2rem; margin-bottom: 10px;">📜</span>
                <span style="font-size: 1.2rem; font-weight: bold; margin-bottom: 6px;"><?= $tr['how_election_works_title'] ?></span>
                <span style="font-size: 0.98rem; color: #444; text-align: center;"><?= $tr['how_election_works_description'] ?></span>
            </a>
            <a href="results.php" class="election-link-card" style="flex: 1 1 220px; max-width: 260px; min-width: 200px; background: var(--sl-cream); border-radius: 18px; box-shadow: var(--sl-shadow); text-decoration: none; color: var(--sl-maroon-dark); padding: 28px 18px 22px 18px; display: flex; flex-direction: column; align-items: center; transition: transform 0.18s, box-shadow 0.18s; border: 2.5px solid var(--sl-gold);">
                <span style="font-size: 2.2rem; margin-bottom: 10px;">🏆</span>
                <span style="font-size: 1.2rem; font-weight: bold; margin-bottom: 6px;"><?= $tr['election_results_title'] ?></span>
                <span style="font-size: 0.98rem; color: #444; text-align: center;"><?= $tr['election_results_description'] ?></span>
            </a>
            <a href="after-election.php" class="election-link-card" style="flex: 1 1 220px; max-width: 260px; min-width: 200px; background: var(--sl-cream); border-radius: 18px; box-shadow: var(--sl-shadow); text-decoration: none; color: var(--sl-maroon-dark); padding: 28px 18px 22px 18px; display: flex; flex-direction: column; align-items: center; transition: transform 0.18s, box-shadow 0.18s; border: 2.5px solid var(--sl-gold);">
                <span style="font-size: 2.2rem; margin-bottom: 10px;">🔎</span>
                <span style="font-size: 1.2rem; font-weight: bold; margin-bottom: 6px;"><?= $tr['after_election_title'] ?></span>
                <span style="font-size: 0.98rem; color: #444; text-align: center;"><?= $tr['after_election_description'] ?></span>
            </a>
        </div>
        <!-- END: Election Info Quick Links -->
    </div>
    <div style="height: 36px;"></div>
    <?php
    // Show latest published results summary on home
    require_once __DIR__ . '/../src/models/Database.php';
    $dbHome = new DatabaseModel();
    $published = $dbHome->query("SELECT * FROM election_config WHERE results_published = 1 ORDER BY end_date DESC LIMIT 1")->fetch();
    if ($published) {
        echo '<div class="main-card" id="home-results" style="margin-bottom:24px;">';
        echo '<h2 class="section-title">' . $tr['latest_results'] . '</h2>';
        echo '<p class="text-muted">' . htmlspecialchars($published['election_name']) . ' (' . htmlspecialchars($published['end_date']) . ')</p>';
        echo '<div class="row g-3 align-items-center">';
        echo '  <div class="col-md-4 text-center"><div id="winnerBoxHome"></div></div>';
        echo '  <div class="col-md-8"><canvas id="homeResultsChart"></canvas></div>';
        echo '</div>';
        echo '<div class="mt-3"><a class="btn btn-primary" href="results.php?election_id=' . $published['config_id'] . '">' . $tr['view_full_results'] . '</a></div>';
        echo '</div>';
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
        echo '<script>';
        echo 'fetch("../src/controllers/ResultsController.php?json=1&election_id=' . $published['config_id'] . '")';
        echo '.then(r=>r.json())';
        echo '.then(d=>{';
        echo '  const finalRound = d.rounds ? Object.values(d.rounds).pop() : {};';
        echo '  const labels = d.candidates.map(c=>c.full_name);';
        echo '  const votes = d.candidates.map(c=>finalRound[c.candidate_id]||0);';
        echo '  const ctx = document.getElementById("homeResultsChart").getContext("2d");';
        echo '  new Chart(ctx,{';
        echo '    type:"bar",';
        echo '    data:{';
        echo '      labels:labels,';
        echo '      datasets:[{';
        echo '        label:"' . $tr['votes'] . '",';
        echo '        data:votes,';
        echo '        backgroundColor:["rgba(141,21,58,0.8)","rgba(0,106,78,0.8)","rgba(255,128,0,0.8)","rgba(0,51,102,0.8)","rgba(255,215,0,0.8)"]';
        echo '      }]';
        echo '    },';
        echo '    options:{';
        echo '      plugins:{';
        echo '        legend:{display:false},';
        echo '        title:{display:true,text:"' . $tr['election_results_chart'] . '"}';
        echo '      },';
        echo '      responsive:true';
        echo '    }';
        echo '  });';
        echo '  const winner = d.candidates.find(c=>c.candidate_id==d.winner);';
        echo '  const img = (winner && winner.photo_path) ? ("../assets/images/" + winner.photo_path) : "../assets/images/election-logo.jpg";';
        echo '  const box = document.getElementById("winnerBoxHome");';
        echo '  if (box) {';
        echo '    box.innerHTML = \'<div class="card p-3" style="border:2px solid var(--sl-gold);"><img src="\'+img+\'" alt="Winner" style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:3px solid var(--sl-gold);margin-bottom:8px;"><div><strong>' . $tr['winner'] . ':</strong><br>\'+(winner?winner.full_name:"N/A")+\'</div></div>\';';
        echo '  }';
        echo '});';
        echo '</script>';
    }
    ?>
    <div class="main-card" id="home-body">
        <section id="why-vote">
            <h3 class="section-title"><?= $tr['why_voting_matters'] ?></h3>
            <p><?= $tr['why_voting_matters_text1'] ?></p>
            <p><?= $tr['why_voting_matters_text2'] ?></p>
        </section>
        <section>
            <h3 class="section-title"><?= $tr['use_your_vote_title'] ?></h3>
            <p><?= $tr['use_your_vote_text1'] ?></p>
            <p><?= $tr['use_your_vote_text2'] ?></p>
            <p><?= $tr['use_your_vote_text3'] ?></p>
        </section>
    </div>
    </main>

    <!-- Reviews & Queries Section -->
    <div class="container mt-5 mb-5">
        <div class="main-card" id="reviews-queries" style="border:2px solid var(--sl-gold); box-shadow: var(--sl-shadow);">
            <h2 class="section-title mb-3"><?= $tr['reviews_queries'] ?></h2>
            <form method="POST" action="reviews_handler.php" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="name" placeholder="<?= $tr['your_name'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <input type="email" class="form-control" name="email" placeholder="<?= $tr['your_email'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="subject" placeholder="<?= $tr['subject'] ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <textarea class="form-control" name="message" rows="3" placeholder="<?= $tr['type_your_review_or_question'] ?>" required></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success"><?= $tr['send'] ?></button>
                    </div>
                </div>
            </form>
            <hr>
            <h4 class="mb-3"><?= $tr['recent_reviews_queries'] ?></h4>
            <div id="reviews-list">
                <?php
                require_once __DIR__ . '/../src/models/Database.php';
                $db = new DatabaseModel();
                $reviews = $db->query("SELECT * FROM reviews_queries ORDER BY created_at DESC LIMIT 5")->fetchAll();
                if ($reviews && count($reviews) > 0) {
                    foreach ($reviews as $r) {
                        echo '<div class="mb-3 p-3" style="background:var(--sl-cream);border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">';
                        echo '<strong>' . htmlspecialchars($r['name']) . '</strong> <span class="text-muted" style="font-size:0.9em;">(' . htmlspecialchars($r['created_at']) . ')</span><br>';
                        echo '<span>' . nl2br(htmlspecialchars($r['message'])) . '</span>';
                        if (!empty($r['reply'])) {
                            echo '<div class="mt-2 p-2" style="background:#f6f6f6;border-left:4px solid var(--sl-maroon);"><strong>' . $tr['admin_reply'] . ':</strong> ' . nl2br(htmlspecialchars($r['reply'])) . '</div>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div class="text-muted">' . $tr['no_reviews_queries_yet'] . '</div>';
                }
                ?>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?> 