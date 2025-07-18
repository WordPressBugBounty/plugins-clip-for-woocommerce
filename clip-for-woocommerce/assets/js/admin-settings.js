jQuery(
	function($) {

		//
		// LOGO
		//
		var url = ClipSettings.logotypeUrl;
		$( '.logotype_clip' ).remove();
		var image = new Image();
		image.src = url;
		image.classList.add( 'logotype_clip' );
		$( '.wrap.woocommerce h2' ).first().prepend( image );

		//
		// OVERRIDE PAYMENT
		//

		const $paymentOverrideCheckbox          = $( '#woocommerce_wc_clip_wc_clip_payment_override' );
        const $paymentTypesContainer            = $( '#wc_clip_payment_type_container' );
		const $paymentInstallmentsContainer     = $( '#wc_clip_payment_installments_container' );
		const $paymentBrandsContainer           = $( '#wc_clip_payment_brands_container' );
		const $paymentInstallmentsLabel         = $( 'label[for="woocommerce_wc_clip_wc_clip_payment_installments_enabled"]' );
		const $paymentInstallmentsCheckboxLabel = $( 'label[for="woocommerce_wc_clip_wc_clip_payment_installments_enabled"]:has(input[type="checkbox"])' );
		const $paymentInstallmentsCheckbox      = $( '#woocommerce_wc_clip_wc_clip_payment_installments_enabled' );

		function togglePaymentContainers() {
			if ($paymentOverrideCheckbox.is( ':checked' )) {
				$paymentTypesContainer.show();
				$paymentBrandsContainer.show();
				$paymentInstallmentsLabel.show();
				$paymentInstallmentsCheckboxLabel.show();

				if ($paymentInstallmentsCheckbox.is( ':checked' )) {
					$paymentInstallmentsContainer.show();

				} else {
					$paymentInstallmentsContainer.hide();
				}

			} else {
				$paymentTypesContainer.hide();
				$paymentBrandsContainer.hide();
				$paymentInstallmentsContainer.hide();
				$paymentInstallmentsLabel.hide();
				$paymentInstallmentsCheckboxLabel.hide();
			}
		}

		$paymentOverrideCheckbox.on(
			'change',
			function() {
				togglePaymentContainers();
			}
		);

		$paymentInstallmentsCheckbox.on(
			'change',
			function() {
				togglePaymentContainers();
			}
		);

		togglePaymentContainers();

		//
		// PAYMENT TYPES
		//
		const $paymentTypes           = $( '.payment_types input[type="checkbox"]' );
		const $cardsCheckbox          = $( '#woocommerce_wc_clip_wc_clip_payment_type\\[cards\\]' );
		const $cardsOptionsWrapper    = $( '.cards-options' );
		const $cardsOptionsLabel      = $( '.cards-options-label' );
		const $cardsOptionsCheckboxes = $( '.cards-options input[type="checkbox"]' );

		toggleCardsOptions();

		$paymentTypes.on(
			'change',
			function () {
				const $checked = $paymentTypes.filter( ':checked' );

				if ($checked.length === 0) {
					$( this ).prop( 'checked', true );
				}

				toggleCardsOptions();
			}
		);

		$cardsOptionsCheckboxes.on(
			'change',
			function () {
				const $checked = $cardsOptionsCheckboxes.filter( ':checked' );
				if ($cardsCheckbox.is( ':checked' ) && $checked.length === 0) {
					$( this ).prop( 'checked', true );
				}
			}
		);

		function toggleCardsOptions() {
			if ($cardsCheckbox.is( ':checked' )) {
				$cardsOptionsWrapper.slideDown();
				$cardsOptionsLabel.slideDown();
			} else {
				$cardsOptionsWrapper.slideUp();
				$cardsOptionsLabel.slideUp();
			}
		}

		//
		// BRANDS
		//
		const checkboxes = $( '.payment_card_brands input[type="checkbox"]' );

		checkboxes.on(
			'change',
			function(e) {
				const checked = checkboxes.filter( ':checked' );

				if (checked.length === 0) {
					e.preventDefault();
					$( this ).prop( 'checked', true );
				}
			}
		);

		//
		// INSTALLMENTS
		//
		function checkAtLeastOneActive() {
			if ($( '.installment-checkbox:checked' ).length === 0) {
				$( '#min_amount_3' ).val( 300 ).prop( 'disabled', false );
				$( '.installment-checkbox[data-id="3"]' ).prop( 'checked', true );
			}
		}

		function toggleInstallmentsContainer() {
			const $mainCheckbox = $( '#woocommerce_wc_clip_wc_clip_payment_installments_enabled' );
			const $container    = $( '#wc_clip_payment_installments_container' );

			if ($mainCheckbox.is( ':checked' )) {
				$container.show();
				checkAtLeastOneActive();
			} else {
				$container.hide();
				$( '.installment-checkbox' ).prop( 'checked', false );
				$( '.min-amount-input' ).prop( 'disabled', true ).val( '' );
			}
		}

		$( '#woocommerce_wc_clip_wc_clip_payment_installments_enabled' ).on( 'change', toggleInstallmentsContainer );
		$( '.installment-checkbox' ).on(
			'change',
			function() {
				let id     = $( this ).data( 'id' );
				let $field = $( '#min_amount_' + id );
				if ($( this ).is( ':checked' )) {
					$field.prop( 'disabled', false );
				} else {
					$field.prop( 'disabled', true ).val( '' );
				}
				checkAtLeastOneActive();
			}
		);

		$( '.installment-checkbox' ).each(
			function() {
				let id     = $( this ).data( 'id' );
				let $field = $( '#min_amount_' + id );
				if ($( this ).is( ':checked' )) {
					$field.prop( 'disabled', false );
				} else {
					$field.prop( 'disabled', true ).val( '' );
				}
			}
		);
		toggleInstallmentsContainer();
		checkAtLeastOneActive();
	}
);
