// register.php
function popupToggle() {
	const popup = document.querySelector('.popup');

	popup.classList.toggle('active');
}

// payment.js
function allowOnlyNumbers(input) {
	input.addEventListener('input', function () {
		this.value = this.value.replace(/[^\d]/g, '');
	});
}
