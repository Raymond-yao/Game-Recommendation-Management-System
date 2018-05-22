
$(function () {
	$('#avatar').popover({
		trigger: 'hover',
		container: 'body',
		offset: 0,
		html: true,
		content:'<div class="popover-bg"><div class="avatar-bg"></div><div class="avatar-title">Raymond</div><div class="list-count">Recommendation List: 0</div></div>'
	})
})