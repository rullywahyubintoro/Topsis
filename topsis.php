<?php
/**
 * Sumber : https://tugasakhir.id/contoh-perhitungan-spk-metode-topsis/
 */

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
	$arrAlternatif = [
		'kode' => $rAlternatif['kode'],
		'nama' => $rAlternatif['nama'],
	];
	foreach($kriteria as $rKriteria){
		$arrAlternatif[$rKriteria['kode']] = ($rAlternatif[$rKriteria['kode']] ** 2);
	}
	$resAlternatif[] = $arrAlternatif;
}

foreach($kriteria as $rKriteria){
	$totalAlternatif[$rKriteria['kode']] = array_sum(array_column($resAlternatif,$rKriteria['kode']));
}

$normalisasi = [];
foreach($alternatif as $rAlternatif){
	$arrNormalisasi = [
		'kode' => $rAlternatif['kode'],
		'nama' => $rAlternatif['nama'],
	];
	foreach($kriteria as $rKriteria){
		$arrNormalisasi[$rKriteria['kode']] = ($rAlternatif[$rKriteria['kode']] / sqrt($totalAlternatif[$rKriteria['kode']]));
	}
	$normalisasi[] = $arrNormalisasi;
}

/** Normalisasi Terbobot */
$normalisasiTerbobot = [];
foreach($normalisasi as $rNormalisasi){
	$arrNormalisasiTerbobot = [
		'kode' => $rNormalisasi['kode'],
		'nama' => $rNormalisasi['nama'],
	];
	foreach($kriteria as $rKriteria){
		$arrNormalisasiTerbobot[$rKriteria['kode']] = ($rNormalisasi[$rKriteria['kode']] * $kriteria[array_search($rKriteria['kode'], array_column($kriteria, 'kode'))]['bobot']);
	}
	$normalisasiTerbobot[] = $arrNormalisasiTerbobot;
}

/** Matriks Sulusi Ideal */
foreach($kriteria as $rKriteria){
	/** Positif => (max|benefit), (min|cost) */
	$matriksSolusiIdeal['positif'][$rKriteria['kode']] = $kriteria[array_search($rKriteria['kode'], array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? max(array_column($normalisasiTerbobot,$rKriteria['kode'])) : min(array_column($normalisasiTerbobot,$rKriteria['kode']));
	/** Negatif => (min|benefit), (max|cost) */
	$matriksSolusiIdeal['negatif'][$rKriteria['kode']] = $kriteria[array_search($rKriteria['kode'], array_column($kriteria, 'kode'))]['atribut'] == 'benefit' ? min(array_column($normalisasiTerbobot,$rKriteria['kode'])) : max(array_column($normalisasiTerbobot,$rKriteria['kode']));
}

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
