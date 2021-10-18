<?php

/** Data */
$kriteria = [
	[
		'kode' => 'c01',
		'nama' => 'Jumlah Penghasilan Orangtua',
		'atribut' => 'cost', /** cost -> Semakin kecil nilai semakin bagus */
		'bobot' => 5,
	],
	[
		'kode' => 'c02',
		'nama' => 'Jumlah Tanggungan Orangtua',
		'atribut' => 'benefit', /** benefit -> Semakin besar nilai semakin bagus */
		'bobot' => 3,
	],
	[
		'kode' => 'c03',
		'nama' => 'Jarak Tempat Tinggal',
		'atribut' => 'cost',
		'bobot' => 4,
	],
	[
		'kode' => 'c04',
		'nama' => 'Nilai Rata-rata Ujian Nasional',
		'atribut' => 'benefit',
		'bobot' => 5,
	],
	[
		'kode' => 'c05',
		'nama' => 'Kesanggupan Tinggal di Asrama',
		'atribut' => 'benefit',
		'bobot' => 2,
	],
];

$alternatif = [
	[
		'kode' => 'a01',
		'nama' => 'Alternatif 1',
		'c01' => 5,
		'c02' => 2,
		'c03' => 1,
		'c04' => 5,
		'c05' => 1,
	],
	[
		'kode' => 'a02',
		'nama' => 'Alternatif 2',
		'c01' => 5,
		'c02' => 1,
		'c03' => 1,
		'c04' => 3,
		'c05' => 1,
	],
	[
		'kode' => 'a03',
		'nama' => 'Alternatif 3',
		'c01' => 5,
		'c02' => 3,
		'c03' => 1,
		'c04' => 4,
		'c05' => 1,
	],
];

/** Perhitungan Topsis */
/** Normalisasi */
$resAlternatif = [];
foreach($alternatif as $rAlternatif){
	$resAlternatif[] = [
		'kode' => $rAlternatif['kode'],
		'nama' => $rAlternatif['nama'],
		'c01' => ($rAlternatif['c01'] ** 2),
		'c02' => ($rAlternatif['c02'] ** 2),
		'c03' => ($rAlternatif['c03'] ** 2),
		'c04' => ($rAlternatif['c04'] ** 2),
		'c05' => ($rAlternatif['c05'] ** 2),
	];
}

$totalAlternatif = [
	'c01' => array_sum(array_column($resAlternatif,'c01')),
	'c02' => array_sum(array_column($resAlternatif,'c02')),
	'c03' => array_sum(array_column($resAlternatif,'c03')),
	'c04' => array_sum(array_column($resAlternatif,'c04')),
	'c05' => array_sum(array_column($resAlternatif,'c05')),
];

$normalisasi = [];
foreach($alternatif as $rAlternatif){
	$normalisasi[] = [
		'kode' => $rAlternatif['kode'],
		'nama' => $rAlternatif['nama'],
		'c01' => ($rAlternatif['c01'] / sqrt($totalAlternatif['c01'])),
		'c02' => ($rAlternatif['c02'] / sqrt($totalAlternatif['c02'])),
		'c03' => ($rAlternatif['c03'] / sqrt($totalAlternatif['c03'])),
		'c04' => ($rAlternatif['c04'] / sqrt($totalAlternatif['c04'])),
		'c05' => ($rAlternatif['c05'] / sqrt($totalAlternatif['c05'])),
	];
}

/** Normalisasi Terbobot */
$normalisasiTerbobot = [];
foreach($normalisasi as $rNormalisasi){
	$normalisasiTerbobot[] = [
		'kode' => $rNormalisasi['kode'],
		'nama' => $rNormalisasi['nama'],
		'c01' => ($rNormalisasi['c01'] * $kriteria[array_search('c01', array_column($kriteria, 'kode'))]['bobot']),
		'c02' => ($rNormalisasi['c02'] * $kriteria[array_search('c02', array_column($kriteria, 'kode'))]['bobot']),
		'c03' => ($rNormalisasi['c03'] * $kriteria[array_search('c03', array_column($kriteria, 'kode'))]['bobot']),
		'c04' => ($rNormalisasi['c04'] * $kriteria[array_search('c04', array_column($kriteria, 'kode'))]['bobot']),
		'c05' => ($rNormalisasi['c05'] * $kriteria[array_search('c05', array_column($kriteria, 'kode'))]['bobot']),
	];
}

/** Matriks Sulusi Ideal */
/** Positif => (max|benefit), (min|cost) */
$matriksSolusiIdeal['positif'] = [
	'c01' => $kriteria[array_search('c01', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,'c01')) : min(array_column($normalisasiTerbobot,'c01')),
	'c02' => $kriteria[array_search('c02', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,'c02')) : min(array_column($normalisasiTerbobot,'c02')),
	'c03' => $kriteria[array_search('c03', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,'c03')) : min(array_column($normalisasiTerbobot,'c03')),
	'c04' => $kriteria[array_search('c04', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,'c04')) : min(array_column($normalisasiTerbobot,'c04')),
	'c05' => $kriteria[array_search('c05', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,'c05')) : min(array_column($normalisasiTerbobot,'c05')),
];
/** Negatif => (min|benefit), (max|cost) */
$matriksSolusiIdeal['negatif'] = [
	'c01' => $kriteria[array_search('c01', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,'c01')) : max(array_column($normalisasiTerbobot,'c01')),
	'c02' => $kriteria[array_search('c02', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,'c02')) : max(array_column($normalisasiTerbobot,'c02')),
	'c03' => $kriteria[array_search('c03', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,'c03')) : max(array_column($normalisasiTerbobot,'c03')),
	'c04' => $kriteria[array_search('c04', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,'c04')) : max(array_column($normalisasiTerbobot,'c04')),
	'c05' => $kriteria[array_search('c05', array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,'c05')) : max(array_column($normalisasiTerbobot,'c05')),
];

/** Total */
$total = [];
foreach($normalisasiTerbobot as $rNormalisasiTerbobot){
	$totalPositif = 0;
	$totalNegatif = 0;
	foreach($kriteria as $rKriteria){
		$totalPositif += ($rNormalisasiTerbobot[$rKriteria['kode']] - $matriksSolusiIdeal['positif'][$rKriteria['kode']]) ** 2;
		$totalNegatif += ($rNormalisasiTerbobot[$rKriteria['kode']] - $matriksSolusiIdeal['negatif'][$rKriteria['kode']]) ** 2;
	}
	$totalPositif = sqrt($totalPositif);
	$totalNegatif = sqrt($totalNegatif);
	$preferensi = $totalNegatif > 0 ? $totalNegatif / ($totalPositif + $totalNegatif) : 0;

	$total[] = [
		'kode' => $rNormalisasiTerbobot['kode'],
		'nama' => $rNormalisasiTerbobot['nama'],
		'positif' => $totalPositif,
		'negatif' => $totalNegatif,
		'preferensi' => $preferensi,
	];
}

/** SORT */
$kTotal = array_column($total, 'preferensi');
array_multisort($kTotal, SORT_DESC, $total);
echo json_encode($total);
?>
