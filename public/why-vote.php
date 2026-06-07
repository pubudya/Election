<?php 
$activePage = 'why'; 
include 'header.php'; 
?>
<main class="container">
    <div class="main-card" id="why-vote">
        <h2 class="section-title"><?= $tr['why_vote_matters_title'] ?></h2>
        
        <h3 class="mt-4 mb-2"><?= $tr['shape_future_country'] ?></h3>
        <p><?= $tr['shape_future_description'] ?></p>
        
        <h3 class="mt-4 mb-2"><?= $tr['hold_leaders_accountable'] ?></h3>
        <p><?= $tr['hold_leaders_description'] ?></p>
        
        <h3 class="mt-4 mb-2"><?= $tr['address_national_global_challenges'] ?></h3>
        <p><?= $tr['address_challenges_description'] ?></p>
        
        <h3 class="mt-4 mb-2"><?= $tr['protect_democracy'] ?></h3>
        <p><?= $tr['protect_democracy_description'] ?></p>
        
        <h3 class="mt-4 mb-2"><?= $tr['your_voice_matters'] ?></h3>
        <p><?= $tr['your_voice_description'] ?></p>
        
        <h3 class="mt-4 mb-2"><?= $tr['what_happens_if_dont_vote'] ?></h3>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['weak_mandates'] ?></span> → <?= $tr['weak_mandates_result'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['policies_ignore_communities'] ?></span></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['corruption_authoritarianism'] ?></span></li>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?> 