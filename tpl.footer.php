
<hr />

<details>
	<summary>Jira requests</summary>
	<pre style="white-space: pre-line; margin: 0"><?= implode('<br />', $jira_history) ?></pre>
</details>

<pre><?= number_format(microtime(1) - $_start, 3) ?> s</pre>

<script>
$$('a[data-confirm]').on('click', function(e) {
	if ( !confirm(this.data('confirm')) ) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
	}
});

$$('button[data-preview]').on('click', function(e) {
	e.preventDefault();
	var btn = this;
	var text = $(btn.data('preview'), true).value;
	document.body.addClass('loading');
	$.post('new.php', 'preview=' + encodeURIComponent(text)).on('success', function(e, html) {
		document.el('div', {"class": 'markup', "style": 'position: relative; outline: solid 2px blue'})
			.setHTML('<button type="button" onclick="return this.parentNode.remove(), false" style="position: absolute; top: 10px; right: 10px">x</button>' + html)
			.injectAfter(btn.selfOrAncestor('p'));
		document.body.removeClass('loading');
	});
});

$$('a.ajax:not(.ajaxed)').on('click', function(e) {
	e.preventDefault();
	document.body.addClass('loading');
	$.get(this.href).on('done', function(e, rsp) {
		if ( rsp.substr(0, 2) == 'OK' ) {
			location.reload();
		}
		else {
			document.body.removeClass('loading');
			alert(rsp);
		}
	});
}).addClass('ajaxed');
</script>

</body>

</html>
