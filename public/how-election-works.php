<?php 
$activePage = 'how'; 
include 'header.php'; 
?>
<main class="container">
    <div class="main-card" id="how-election-works">
        <h2 class="section-title"><?= $tr['how_sri_lanka_presidential_election_works'] ?></h2>
        <p><?= $tr['presidential_election_description'] ?></p>
        <h3 class="mt-4 mb-2"><?= $tr['what_is_presidential_election'] ?></h3>
        <p><?= $tr['presidential_election_explanation'] ?></p>
        <h3 class="mt-4 mb-2"><?= $tr['how_president_elected'] ?></h3>
        <ul>
            <li><strong><?= $tr['voting_system'] ?>:</strong> <?= $tr['voting_system_description'] ?></li>
            <li><?= $tr['second_round_description'] ?></li>
            <li><strong><?= $tr['term'] ?>:</strong> <?= $tr['term_description'] ?></li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['voting_in_sri_lanka'] ?></h3>
        <ul>
            <li><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['who_can_vote'] ?></span><br>
                <?= $tr['voting_eligibility'] ?><br>
                <?= $tr['voting_compulsory'] ?>
            </li>
            <li class="mt-2"><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['how_voting_works'] ?></span><br>
                <ul>
                    <li><strong><?= $tr['voter_registration'] ?>:</strong> <?= $tr['voter_registration_description'] ?></li>
                    <li><strong><?= $tr['polling_day'] ?>:</strong> <?= $tr['polling_day_description'] ?></li>
                    <li><strong><?= $tr['counting'] ?>:</strong> <?= $tr['counting_description'] ?></li>
                </ul>
            </li>
            <li class="mt-2"><span style="color:var(--sl-green);font-weight:bold;">&#10003; <?= $tr['key_factors'] ?></span><br>
                <ul>
                    <li><strong><?= $tr['campaigns'] ?>:</strong> <?= $tr['campaigns_description'] ?></li>
                    <li><strong><?= $tr['debates'] ?>:</strong> <?= $tr['debates_description'] ?></li>
                    <li><strong><?= $tr['voter_turnout'] ?>:</strong> <?= $tr['voter_turnout_description'] ?></li>
                </ul>
            </li>
        </ul>
        <h3 class="mt-4 mb-2"><?= $tr['president_powers'] ?></h3>
        <ul>
            <li><strong><?= $tr['executive_authority'] ?>:</strong> <?= $tr['executive_authority_description'] ?></li>
            <li><strong><?= $tr['national_security'] ?>:</strong> <?= $tr['national_security_description'] ?></li>
            <li><strong><?= $tr['economic_policy'] ?>:</strong> <?= $tr['economic_policy_description'] ?></li>
            <li><strong><?= $tr['foreign_relations'] ?>:</strong> <?= $tr['foreign_relations_description'] ?></li>
            <li><strong><?= $tr['judicial_appointments'] ?>:</strong> <?= $tr['judicial_appointments_description'] ?></li>
        </ul>
        <p class="mb-3"><?= $tr['power_sharing_note'] ?></p>
        <h3 class="mt-4 mb-2"><?= $tr['election_frequency'] ?></h3>
        <ul>
            <li><?= $tr['election_frequency_description'] ?></li>
            <li><strong><?= $tr['next_election'] ?>:</strong> <?= $tr['next_election_date'] ?></li>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?> 