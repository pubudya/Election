<?php 
$activePage = 'candidates'; 
include 'header.php'; 
require_once __DIR__ . '/../src/models/Database.php';
$db = new DatabaseModel();
$candidates = $db->query('SELECT * FROM candidates WHERE is_active = 1')->fetchAll();

function localizedValue(array $row, string $baseKey, string $lang): string {
    $langKey = $baseKey . '_' . $lang;
    if (!empty($row[$langKey])) {
        return (string)$row[$langKey];
    }
    return (string)($row[$baseKey] ?? '');
}

// Simple language generator for well-known party and province names.
// If DB does not have *_si or *_ta columns, we localize common terms at render time.
function localizeKnownTerm(string $value, string $lang): string {
	$raw = trim($value);
	if ($raw === '') {
		return $value;
	}
	$provinceMap = [
		'si' => [
			'Central' => 'මධ්‍යම',
			'Eastern' => 'නැගෙනහිර',
			'Northern' => 'උතුර',
			'North Central' => 'උතුරු මැද',
			'North Western' => 'වයඹ',
			'Sabaragamuwa' => 'සබරගමු',
			'Southern' => 'දකුණ',
			'Uva' => 'ඌව',
			'Western' => 'බස්නාහිර',
		],
		'ta' => [
			'Central' => 'மத்திய',
			'Eastern' => 'கிழக்கு',
			'Northern' => 'வடக்கு',
			'North Central' => 'வட மத்திய',
			'North Western' => 'வட மேற்கு',
			'Sabaragamuwa' => 'சபரகமுவ',
			'Southern' => 'தெற்கு',
			'Uva' => 'ஊவா',
			'Western' => 'மேற்கு',
		],
	];
	$partyMap = [
		'si' => [
			'United National Party' => 'එක්සත් ජාතික පක්ෂය',
			'Samagi Jana Balawegaya' => 'සමගි ජන බලවේගය',
			'Sri Lanka Podujana Peramuna' => 'ශ්‍රී ලංකා පොදුජන පෙරමුණ',
			"National People's Power" => 'ජාතික ජන බලවේගය',
			'Janatha Vimukthi Peramuna' => 'ජනතා විමුක්ති පෙරමුණ',
		],
		'ta' => [
			'United National Party' => 'ஐக்கிய தேசியக் கட்சி',
			'Samagi Jana Balawegaya' => 'சமகி ஜன பலவேகய',
			'Sri Lanka Podujana Peramuna' => 'இலங்கை பொதுஜன பெரமுன',
			"National People's Power" => 'தேசிய மக்கள் சக்தி',
			'Janatha Vimukthi Peramuna' => 'ஜனதா விமுக்தி பெரமுன',
		],
	];
	$maps = [
		$provinceMap[$lang] ?? [],
		$partyMap[$lang] ?? [],
	];
	foreach ($maps as $map) {
		if (isset($map[$raw])) {
			return $map[$raw];
		}
	}
	return $value;
}
?>
<main class="container">
    <div class="main-card" id="candidates">
        <h2 class="section-title"><?= $tr['candidates_title'] ?></h2>
        <?php if (empty($candidates)): ?>
            <div class="alert alert-info text-center"><?= $tr['no_candidates_yet'] ?></div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($candidates as $candidate): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card candidate-card h-100 text-center p-2">
                        <?php if (!empty($candidate['party_logo'])): ?>
                            <img src="<?php echo '../assets/images/' . htmlspecialchars($candidate['party_logo']); ?>" alt="<?= $tr['party_logo_alt'] ?>" style="width:40px;height:40px;object-fit:contain;margin:10px auto 0 auto;display:block;">
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($candidate['photo_path'] ? '../assets/images/' . $candidate['photo_path'] : '../assets/images/election-logo.jpg'); ?>" class="card-img-top candidate-img mx-auto mt-2" alt="<?= $tr['candidate_photo_alt'] ?>" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:2px solid var(--sl-gold);">
                        <div class="card-body">
                            <h5 class="card-title mb-1" style="color:var(--sl-maroon);font-weight:600;">
                                <?php echo htmlspecialchars(localizedValue($candidate, 'full_name', $lang)); ?>
                            </h5>
                            <div class="mb-1" style="color:var(--sl-blue);font-size:1.05em;">
                                <strong><?= $tr['party_label'] ?></strong> <?php $party = localizedValue($candidate, 'party', $lang); echo htmlspecialchars($lang === 'en' ? $party : localizeKnownTerm($party, $lang)); ?>
                            </div>
                            <?php if (!empty($candidate['party_symbol'])): ?>
                                <div class="mb-1"><strong><?= $tr['symbol_label'] ?></strong> <?php echo htmlspecialchars($candidate['party_symbol']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($candidate['candidate_number'])): ?>
                                <div class="mb-1"><strong><?= $tr['number_label'] ?></strong> <?php echo htmlspecialchars($candidate['candidate_number']); ?></div>
                            <?php endif; ?>
                            <?php 
                                $provinceLocalized = localizedValue($candidate, 'province', $lang);
                                if (!empty($provinceLocalized)):
                            ?>
                                <div class="mb-1"><strong><?= $tr['province_label'] ?></strong> <?php echo htmlspecialchars($lang === 'en' ? $provinceLocalized : localizeKnownTerm($provinceLocalized, $lang)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?> 