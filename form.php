<!doctype html>

<meta charset="utf-8">
<title>Article XML</title>

<form>
	<select name="scheme">
		<option value="gist" <? if ($scheme == 'gist'): ?>selected<? endif; ?>>Gist (article.xml)</option>
		<option value="doi" <? if ($scheme == 'doi'): ?>selected<? endif; ?>>DOI</option>
		<option value="pmc" <? if ($scheme == 'pmc'): ?>selected<? endif; ?>>PubMed Central</option>
	</select>

	<input type="text" size="40" name="id" autocomplete="off" value="<? htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">

	<button type="submit">Fetch XML</button>
</form>