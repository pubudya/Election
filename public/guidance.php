<?php 
$activePage = 'guidance'; 
include 'header.php'; 
?>
<main class="container">
    <div class="main-card" id="guidance">
        <h2 class="section-title"><?= $tr['guidance_title'] ?></h2>
        
        <div class="accordion" id="guidanceAccordion">
            <!-- How to Register as a Voter -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="registrationHeader">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#registrationCollapse" aria-expanded="true" aria-controls="registrationCollapse">
                        <?= $tr['how_to_register_as_voter'] ?>
                    </button>
                </h3>
                <div id="registrationCollapse" class="accordion-collapse collapse show" aria-labelledby="registrationHeader" data-bs-parent="#guidanceAccordion">
                    <div class="accordion-body">
                        <h4><?= $tr['step_by_step_guide'] ?></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= $tr['eligibility_requirements'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['age_requirement'] ?></strong> <?= $tr['age_requirement_text'] ?></li>
                                    <li><strong><?= $tr['citizenship_requirement'] ?></strong> <?= $tr['citizenship_requirement_text'] ?></li>
                                    <li><strong><?= $tr['residence_requirement'] ?></strong> <?= $tr['residence_requirement_text'] ?></li>
                                    <li><strong><?= $tr['mental_capacity_requirement'] ?></strong> <?= $tr['mental_capacity_requirement_text'] ?></li>
                                    <li><strong><?= $tr['criminal_record_requirement'] ?></strong> <?= $tr['criminal_record_requirement_text'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><?= $tr['required_documents'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['nic_document'] ?></strong> <?= $tr['nic_document_text'] ?></li>
                                    <li><strong><?= $tr['birth_certificate'] ?></strong> <?= $tr['birth_certificate_text'] ?></li>
                                    <li><strong><?= $tr['address_proof'] ?></strong> <?= $tr['address_proof_text'] ?></li>
                                    <li><strong><?= $tr['passport_photos'] ?></strong> <?= $tr['passport_photos_text'] ?></li>
                                    <li><strong><?= $tr['form_1a'] ?></strong> <?= $tr['form_1a_text'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <h5><?= $tr['registration_process'] ?></h5>
                        <ol>
                            <li><strong><?= $tr['visit_grama_niladhari'] ?></strong> <?= $tr['visit_grama_niladhari_text'] ?></li>
                            <li><strong><?= $tr['submit_application'] ?></strong> <?= $tr['submit_application_text'] ?></li>
                            <li><strong><?= $tr['verification'] ?></strong> <?= $tr['verification_text'] ?></li>
                            <li><strong><?= $tr['approval'] ?></strong> <?= $tr['approval_text'] ?></li>
                            <li><strong><?= $tr['voter_id'] ?></strong> <?= $tr['voter_id_text'] ?></li>
                        </ol>
                        <div class="alert alert-info">
                            <strong><?= $tr['online_registration'] ?></strong> <?= $tr['online_registration_text'] ?> <a href="register.php"><?= $tr['registration_page'] ?></a>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Election Day Procedure -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="electionDayHeader">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#electionDayCollapse" aria-expanded="false" aria-controls="electionDayCollapse">
                        <?= $tr['election_day_procedure'] ?>
                    </button>
                </h3>
                <div id="electionDayCollapse" class="accordion-collapse collapse" aria-labelledby="electionDayHeader" data-bs-parent="#guidanceAccordion">
                    <div class="accordion-body">
                        <h4><?= $tr['election_day_expectations'] ?></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= $tr['before_voting'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['check_polling_station'] ?></strong> <?= $tr['check_polling_station_text'] ?></li>
                                    <li><strong><?= $tr['bring_documents'] ?></strong> <?= $tr['bring_documents_text'] ?></li>
                                    <li><strong><?= $tr['dress_code'] ?></strong> <?= $tr['dress_code_text'] ?></li>
                                    <li><strong><?= $tr['timing'] ?></strong> <?= $tr['timing_text'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><?= $tr['at_polling_station'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['queue'] ?></strong> <?= $tr['queue_text'] ?></li>
                                    <li><strong><?= $tr['identity_check'] ?></strong> <?= $tr['identity_check_text'] ?></li>
                                    <li><strong><?= $tr['ballot_paper'] ?></strong> <?= $tr['ballot_paper_text'] ?></li>
                                    <li><strong><?= $tr['voting_booth'] ?></strong> <?= $tr['voting_booth_text'] ?></li>
                                    <li><strong><?= $tr['ballot_box'] ?></strong> <?= $tr['ballot_box_text'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <h5><?= $tr['voting_process'] ?></h5>
                        <ol>
                            <li><strong><?= $tr['entry'] ?></strong> <?= $tr['entry_text'] ?></li>
                            <li><strong><?= $tr['registration_voting'] ?></strong> <?= $tr['registration_voting_text'] ?></li>
                            <li><strong><?= $tr['ballot_voting'] ?></strong> <?= $tr['ballot_voting_text'] ?></li>
                            <li><strong><?= $tr['marking'] ?></strong> <?= $tr['marking_text'] ?></li>
                            <li><strong><?= $tr['folding'] ?></strong> <?= $tr['folding_text'] ?></li>
                            <li><strong><?= $tr['exit'] ?></strong> <?= $tr['exit_text'] ?></li>
                        </ol>
                        <div class="alert alert-warning">
                            <strong><?= $tr['important_note'] ?></strong> <?= $tr['important_note_text'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voter Rights and Responsibilities -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="rightsHeader">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rightsCollapse" aria-expanded="false" aria-controls="rightsCollapse">
                        <?= $tr['voter_rights_and_responsibilities'] ?>
                    </button>
                </h3>
                <div id="rightsCollapse" class="accordion-collapse collapse" aria-labelledby="rightsHeader" data-bs-parent="#guidanceAccordion">
                    <div class="accordion-body">
                        <h4><?= $tr['your_rights_and_responsibilities'] ?></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= $tr['voter_rights'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['right_to_vote'] ?></strong> <?= $tr['right_to_vote_text'] ?></li>
                                    <li><strong><?= $tr['secret_ballot'] ?></strong> <?= $tr['secret_ballot_text'] ?></li>
                                    <li><strong><?= $tr['equal_treatment'] ?></strong> <?= $tr['equal_treatment_text'] ?></li>
                                    <li><strong><?= $tr['accessibility'] ?></strong> <?= $tr['accessibility_text'] ?></li>
                                    <li><strong><?= $tr['complaint_rights'] ?></strong> <?= $tr['complaint_rights_text'] ?></li>
                                    <li><strong><?= $tr['information'] ?></strong> <?= $tr['information_text'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><?= $tr['voter_responsibilities'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['register'] ?></strong> <?= $tr['register_text'] ?></li>
                                    <li><strong><?= $tr['verify_information'] ?></strong> <?= $tr['verify_information_text'] ?></li>
                                    <li><strong><?= $tr['follow_rules'] ?></strong> <?= $tr['follow_rules_text'] ?></li>
                                    <li><strong><?= $tr['respect_others'] ?></strong> <?= $tr['respect_others_text'] ?></li>
                                    <li><strong><?= $tr['report_issues'] ?></strong> <?= $tr['report_issues_text'] ?></li>
                                    <li><strong><?= $tr['stay_informed'] ?></strong> <?= $tr['stay_informed_text'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <h5><?= $tr['what_you_cannot_do'] ?></h5>
                        <ul>
                            <li><?= $tr['vote_more_than_once'] ?></li>
                            <li><?= $tr['campaign_within_100_meters'] ?></li>
                            <li><?= $tr['take_photographs_inside_polling_stations'] ?></li>
                            <li><?= $tr['intimidate_or_influence_other_voters'] ?></li>
                            <li><?= $tr['wear_clothing_with_political_symbols'] ?></li>
                            <li><?= $tr['bring_weapons_or_dangerous_items'] ?></li>
                        </ul>
                        <div class="alert alert-success">
                            <strong><?= $tr['remember'] ?></strong> <?= $tr['voting_is_both_a_right_and_a_responsibility'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voter Rules and Regulations -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="rulesHeader">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rulesCollapse" aria-expanded="false" aria-controls="rulesCollapse">
                        <?= $tr['voter_rules_and_regulations'] ?>
                    </button>
                </h3>
                <div id="rulesCollapse" class="accordion-collapse collapse" aria-labelledby="rulesHeader" data-bs-parent="#guidanceAccordion">
                    <div class="accordion-body">
                        <h4><?= $tr['official_rules_and_regulations'] ?></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= $tr['election_laws'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['presidential_elections_act'] ?></strong></li>
                                    <li><strong><?= $tr['parliamentary_elections_act'] ?></strong></li>
                                    <li><strong><?= $tr['local_authorities_elections_ordinance'] ?></strong></li>
                                    <li><strong><?= $tr['election_commission_act'] ?></strong></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><?= $tr['key_regulations'] ?></h5>
                                <ul>
                                    <li><strong><?= $tr['voter_registration'] ?></strong> <?= $tr['voter_registration_text'] ?></li>
                                    <li><strong><?= $tr['polling_hours'] ?></strong> <?= $tr['polling_hours_text'] ?></li>
                                    <li><strong><?= $tr['campaign_period'] ?></strong> <?= $tr['campaign_period_text'] ?></li>
                                    <li><strong><?= $tr['exit_polls'] ?></strong> <?= $tr['exit_polls_text'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <h5><?= $tr['prohibited_activities'] ?></h5>
                        <ul>
                            <li><strong><?= $tr['bribery'] ?></strong> <?= $tr['bribery_text'] ?></li>
                            <li><strong><?= $tr['intimidation'] ?></strong> <?= $tr['intimidation_text'] ?></li>
                            <li><strong><?= $tr['false_information'] ?></strong> <?= $tr['false_information_text'] ?></li>
                            <li><strong><?= $tr['multiple_voting'] ?></strong> <?= $tr['multiple_voting_text'] ?></li>
                            <li><strong><?= $tr['unauthorized_access'] ?></strong> <?= $tr['unauthorized_access_text'] ?></li>
                        </ul>
                        <h5><?= $tr['penalties_for_violations'] ?></h5>
                        <ul>
                            <li><strong><?= $tr['voter_fraud'] ?></strong> <?= $tr['voter_fraud_text'] ?></li>
                            <li><strong><?= $tr['campaign_violations'] ?></strong> <?= $tr['campaign_violations_text'] ?></li>
                            <li><strong><?= $tr['intimidation'] ?></strong> <?= $tr['intimidation_text'] ?></li>
                            <li><strong><?= $tr['bribery'] ?></strong> <?= $tr['bribery_text'] ?></li>
                        </ul>
                        <div class="alert alert-danger">
                            <strong><?= $tr['legal_notice'] ?></strong> <?= $tr['violation_of_election_laws_is_a_serious_offense'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.accordion-item {
    border: 1px solid var(--sl-gold);
    border-radius: 8px;
    margin-bottom: 15px;
    background: var(--sl-card);
}

.accordion-button {
    background: linear-gradient(135deg, var(--sl-maroon), #8B0000);
    color: white;
    font-weight: 600;
    border: none;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, var(--sl-maroon), #8B0000);
    color: white;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
}

.accordion-body {
    padding: 25px;
    background: white;
    border-radius: 0 0 8px 8px;
}

.accordion-body h4 {
    color: var(--sl-maroon);
    margin-bottom: 20px;
    font-weight: 600;
}

.accordion-body h5 {
    color: var(--sl-blue);
    margin-top: 20px;
    margin-bottom: 10px;
    font-weight: 600;
}

.accordion-body ul, .accordion-body ol {
    margin-bottom: 15px;
}

.accordion-body li {
    margin-bottom: 8px;
    line-height: 1.6;
}

.alert {
    border-radius: 8px;
    border: none;
    margin-top: 20px;
}

.alert-info {
    background: rgba(13, 202, 240, 0.1);
    color: #055160;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #664d03;
}

.alert-success {
    background: rgba(25, 135, 84, 0.1);
    color: #0f5132;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #721c24;
}
</style>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 