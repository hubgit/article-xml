<?php

ob_start();

$scheme = $_GET['scheme'];
$id = $_GET['id'];

$id = preg_replace('/\s+/', '', $id);

if ($id) {
	switch ($scheme) {
		case 'gist': /* GitHub Gist ID */
			if (preg_match('/^\d+$/', $id)) {
				fetch_gist($id);
			}
			break;

		case 'doi': /* DOI */
			if (strpos($id, '10.') === 0) {
				fetch_doi($id);
			}
			break;

		case 'pmc': /* PubMed Central ID */
			if (preg_match('/^\d+$/', $id)) {
				fetch_pmc($id);
			}
			break;

		default:
			break;
	}
}

$length = ob_get_length();

if ($length) {
	header('Content-Type: application/xml; charset=UTF-8');
	header(sprintf('Content-Disposition: inline; filename="%s.xml"', preg_replace('/\W/', '-', $id)));
	header('Content-Length: ' . $length);
	header('Access-Control-Allow-Origin: *');
} else {
	if ($scheme || $id) {
		print('Unrecognised scheme or ID');
	}
	include __DIR__ . '/form.php';
}

ob_end_flush();

function fetch_gist($id) {
	$url = sprintf('https://raw.github.com/gist/%s/article.xml', $id);
	readfile($url);
}

function fetch_doi($id) {
	list($publisher, $article) = explode('/', $id, 2);

	switch ($publisher) {
		// PLOS ONE
		case '10.1371':
			$params = array(
				'representation' => 'XML',
				'uri' => 'info:doi/' . $id
			);

			$url = 'http://www.plosone.org/article/fetchObjectAttachment.action?' . http_build_query($params);
			break;

		default:
			return;
	}

	readfile($url);
}

function fetch_pmc($id) {
	$params = array(
		'db' => 'pmc',
		'id' => $id,
	);

	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' . http_build_query($params);

	$data = file_get_contents($url);
	$data = preg_replace('/^.+?<pmc-articleset>/s', '<article ', $data);
	$data = preg_replace('/<\/pmc-articleset>.*/s', '</article>', $data);

	print $data;
}

function fetch_pmc_oai($id) {
	$params = array(
		'verb' => 'GetRecord',
		'metadataPrefix' => 'pmc',
		'identifier' => 'oai:pubmedcentral.nih.gov:' . $id,
	);

	$url = 'http://www.pubmedcentral.nih.gov/oai/oai.cgi?' . http_build_query($params);

	$data = file_get_contents($url);
	$data = preg_replace('/^.+?<article\s+xmlns=".+?"/s', '<article ', $data);
	$data = preg_replace('/<\/article>.*/s', '</article>', $data);

	print $data;
}


