<?php 
$activePage = 'after'; 
include 'header.php'; 
?>
<main class="container">
    <div class="main-card" id="after-election">
        <h2 class="section-title"><?= $tr['after_election_title'] ?></h2>
        <p><?= $tr['after_election_description'] ?></p>
        <h3 class="mt-4 mb-2"><?= $tr['official_results_swearing_in'] ?></h3>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['election_commission_announcement'] ?></span> – <?= $tr['election_commission_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['oath_of_office'] ?></span> – <?= $tr['oath_of_office_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['inauguration_speech'] ?></span> – <?= $tr['inauguration_speech_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['formation_of_government'] ?></h3>
        <p><?= $tr['formation_description'] ?></p>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['appointing_prime_minister'] ?></span> – <?= $tr['appointing_prime_minister_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['cabinet_selection'] ?></span> – <?= $tr['cabinet_selection_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['parliamentary_confidence'] ?></span> – <?= $tr['parliamentary_confidence_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['key_early_decisions'] ?></h3>
        <p><?= $tr['early_decisions_description'] ?></p>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['economic_policies'] ?></span> – <?= $tr['economic_policies_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['security_rule_of_law'] ?></span> – <?= $tr['security_rule_of_law_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['foreign_relations_after'] ?></span> – <?= $tr['foreign_relations_after_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['parliaments_role'] ?></h3>
        <p><?= $tr['parliaments_role_description'] ?></p>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['passing_laws'] ?></span> – <?= $tr['passing_laws_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['budget_approval'] ?></span> – <?= $tr['budget_approval_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['checks_balances'] ?></span> – <?= $tr['checks_balances_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['opposition_public_accountability'] ?></h3>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['opposition_leader_role'] ?></span> – <?= $tr['opposition_leader_role_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['media_civil_society'] ?></span> – <?= $tr['media_civil_society_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['public_protests_petitions'] ?></span> – <?= $tr['public_protests_petitions_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['whats_next_sri_lanka'] ?></h3>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['fulfilling_campaign_promises'] ?></span> – <?= $tr['fulfilling_campaign_promises_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['economic_recovery'] ?></span> – <?= $tr['economic_recovery_description'] ?></li>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['national_unity'] ?></span> – <?= $tr['national_unity_description'] ?></li>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?> 