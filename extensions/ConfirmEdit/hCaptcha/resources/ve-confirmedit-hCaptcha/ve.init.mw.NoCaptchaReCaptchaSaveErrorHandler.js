mw.loader.using( 'ext.visualEditor.targetLoader' ).then( function () {
	mw.libs.ve.targetLoader.addPlugin( function () {

		ve.init.mw.hCaptchaSaveErrorHandler = function () {};

		OO.inheritClass( ve.init.mw.hCaptchaSaveErrorHandler, ve.init.mw.SaveErrorHandler );

		ve.init.mw.hCaptchaSaveErrorHandler.static.name = 'confirmEditHCaptcha';

		ve.init.mw.hCaptchaSaveErrorHandler.static.getReadyPromise = function () {
			var onLoadFn = 'onhcaptchaLoadCallback' + Date.now(),
				deferred, config, scriptURL, params;

			if ( !this.readyPromise ) {
				deferred = $.Deferred();
				config = mw.config.get( 'wgConfirmEditConfig' );
				scriptURL = new mw.Uri( config.hCaptchaScriptURL );
				params = { onload: onLoadFn, render: 'explicit' };
				scriptURL.query = $.extend( scriptURL.query, params );

				this.readyPromise = deferred.promise();
				window[ onLoadFn ] = deferred.resolve;
				mw.loader.load( scriptURL.toString() );
			}

			return this.readyPromise;
		};

		ve.init.mw.hCaptchaSaveErrorHandler.static.matchFunction = function ( data ) {
			var captchaData = ve.getProp( data, 'visualeditoredit', 'edit', 'captcha' );

			return !!( captchaData && captchaData.type === 'hcaptcha' );
		};

		ve.init.mw.hCaptchaSaveErrorHandler.static.process = function ( data, target ) {
			var self = this,
				config = mw.config.get( 'wgConfirmEditConfig' ),
				siteKey = config.hCaptchaSiteKey,
				$container = $( '<div>' );

			// Register extra fields
			target.saveFields.wpCaptchaWord = function () {
				// eslint-disable-next-line no-jquery/no-global-selector
				return $( '#h-captcha-response' ).val();
			};

			this.getReadyPromise()
				.then( function () {
					if ( self.widgetId ) {
						window.hcaptcha.reset( self.widgetId );
					} else {
						target.saveDialog.showMessage( 'api-save-error', $container, { wrap: false } );
						self.widgetId = window.hcaptcha.render( $container[ 0 ], {
							sitekey: siteKey,
							callback: function () {
								target.saveDialog.executeAction( 'save' );
							},
							'expired-callback': function () {},
							'error-callback': function () {}
						} );

						target.saveDialog.updateSize();
					}

					target.emit( 'saveErrorCaptcha' );
				} );
		};

		ve.init.mw.saveErrorHandlerFactory.register( ve.init.mw.hCaptchaSaveErrorHandler );

	} );
} );
