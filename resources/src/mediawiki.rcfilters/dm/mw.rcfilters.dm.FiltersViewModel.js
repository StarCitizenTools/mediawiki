( function ( mw, $ ) {
	/**
	 * View model for the filters selection and display
	 *
	 * @mixins OO.EventEmitter
	 * @mixins OO.EmitterList
	 *
	 * @constructor
	 */
	mw.rcfilters.dm.FiltersViewModel = function MwRcfiltersDmFiltersViewModel() {
		// Mixin constructor
		OO.EventEmitter.call( this );
		OO.EmitterList.call( this );

		this.groups = {};

		// Events
		this.aggregate( { update: 'itemUpdate' } );
	};

	/* Initialization */
	OO.initClass( mw.rcfilters.dm.FiltersViewModel );
	OO.mixinClass( mw.rcfilters.dm.FiltersViewModel, OO.EventEmitter );
	OO.mixinClass( mw.rcfilters.dm.FiltersViewModel, OO.EmitterList );

	/* Events */

	/**
	 * @event initialize
	 *
	 * Filter list is initialized
	 */

	/**
	 * @event itemUpdate
	 * @param {mw.rcfilters.dm.FilterItem} item Filter item updated
	 *
	 * Filter item has changed
	 */

	/* Methods */

	/**
	 * Set filters and preserve a group relationship based on
	 * the definition given by an object
	 *
	 * @param {Object} filters Filter group definition
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.initializeFilters = function ( filters ) {
		var i, filterItem,
			model = this,
			items = [];

		// Reset
		this.clearItems();
		this.groups = {};

		$.each( filters, function ( group, data ) {
			model.groups[ group ] = model.groups[ group ] || {};
			model.groups[ group ].filters = model.groups[ group ].filters || [];

			model.groups[ group ].title = data.title;
			model.groups[ group ].type = data.type;

			for ( i = 0; i < data.filters.length; i++ ) {
				filterItem = new mw.rcfilters.dm.FilterItem( data.filters[ i ].name, {
					group: group,
					label: data.filters[ i ].label,
					description: data.filters[ i ].description,
					selected: data.filters[ i ].selected
				} );

				model.groups[ group ].filters.push( filterItem );
				items.push( filterItem );
			}
		} );

		this.addItems( items );
		this.emit( 'initialize' );
	};

	/**
	 * Get the names of all available filters
	 *
	 * @return {string[]} An array of filter names
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getFilterNames = function () {
		return this.getItems().map( function ( item ) { return item.getName(); } );
	};

	/**
	 * Get the object that defines groups and their filter items.
	 * The structure of this response:
	 * {
	 *   groupName: {
	 *     title: {string} Group title
	 *     type: {string} Group type
	 *     filters: {string[]} Filters in the group
	 *   }
	 * }
	 *
	 * @return {Object} Filter groups
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getFilterGroups = function () {
		return this.groups;
	};

	/**
	 * Get the current state of the filters
	 *
	 * @return {Object} Filters current state
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getState = function () {
		var i,
			items = this.getItems(),
			result = {};

		for ( i = 0; i < items.length; i++ ) {
			result[ items[ i ].getName() ] = items[ i ].isSelected();
		}

		return result;
	};

	/**
	 * Analyze the groups and their filters and output an object representing
	 * the state of the parameters they represent.
	 *
	 * @return {Object} Parameter state object
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getParametersFromFilters = function () {
		var i, filterItems, anySelected,
			result = {},
			groupItems = this.getFilterGroups();

		$.each( groupItems, function ( group, data ) {
			if ( data.type === 'send_unselected_if_any' ) {
				filterItems = data.filters;

				// First, check if any of the items are selected at all.
				// If none is selected, we're treating it as if they are
				// all false
				anySelected = filterItems.some( function ( filterItem ) {
					return filterItem.isSelected();
				} );

				// Go over the items and define the correct values
				for ( i = 0; i < filterItems.length; i++ ) {
					result[ filterItems[ i ].getName() ] = anySelected ?
						Number( !filterItems[ i ].isSelected() ) : 0;
				}
			}
		} );

		return result;
	};

	/**
	 * This is the opposite of the #getParametersFromFilters method; this goes over
	 * the parameters and translates into a selected/unselected value in the filters.
	 *
	 * @param {Object} params Parameters query object
	 * @return {Object} Filter state object
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getFiltersFromParameters = function ( params ) {
		var i, filterItem, allItemsInGroup,
			groupMap = {},
			model = this,
			base = this.getParametersFromFilters(),
			// Start with current state
			result = this.getState();

		params = $.extend( {}, base, params );

		$.each( params, function ( paramName, paramValue ) {
			// Find the filter item
			filterItem = model.getItemByName( paramName );

			// Ignore if no filter item exists
			if ( filterItem ) {
				groupMap[ filterItem.getGroup() ] = groupMap[ filterItem.getGroup() ] || {};

				// Mark the group if it has any items that are selected
				groupMap[ filterItem.getGroup() ].hasSelected = (
					groupMap[ filterItem.getGroup() ].hasSelected ||
					!!Number( paramValue )
				);

				// Add the relevant filter into the group map
				groupMap[ filterItem.getGroup() ].filters = groupMap[ filterItem.getGroup() ].filters || [];
				groupMap[ filterItem.getGroup() ].filters.push( filterItem );
			}
		} );

		// Now that we know the groups' selection states, we need to go over
		// the filters in the groups and mark their selected states appropriately
		$.each( groupMap, function ( group, data ) {
			if ( model.groups[ group ].type === 'send_unselected_if_any' ) {
				allItemsInGroup = model.groups[ group ].filters;

				for ( i = 0; i < allItemsInGroup.length; i++ ) {
					filterItem = allItemsInGroup[ i ];

					result[ filterItem.getName() ] = data.hasSelected ?
						// Flip the definition between the parameter
						// state and the filter state
						// This is what the 'toggleSelected' value of the filter is
						!Number( params[ filterItem.getName() ] ) :
						// Otherwise, there are no selected items in the
						// group, which means the state is false
						false;
				}
			}
		} );
		return result;
	};

	/**
	 * Get the item that matches the given name
	 *
	 * @param {string} name Filter name
	 * @return {mw.rcfilters.dm.FilterItem} Filter item
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.getItemByName = function ( name ) {
		return this.getItems().filter( function ( item ) {
			return name === item.getName();
		} )[ 0 ];
	};

	/**
	 * Toggle selected state of items by their names
	 *
	 * @param {Object} filterDef Filter definitions
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.updateFilters = function ( filterDef ) {
		var name, filterItem;

		for ( name in filterDef ) {
			filterItem = this.getItemByName( name );
			filterItem.toggleSelected( filterDef[ name ] );
		}
	};

	/**
	 * Find items whose labels match the given string
	 *
	 * @param {string} str Search string
	 * @return {Object} An object of items to show
	 *  arranged by their group names
	 */
	mw.rcfilters.dm.FiltersViewModel.prototype.findMatches = function ( str ) {
		var i,
			result = {},
			items = this.getItems();

		// Normalize so we can search strings regardless of case
		str = str.toLowerCase();
		for ( i = 0; i < items.length; i++ ) {
			if ( items[ i ].getLabel().toLowerCase().indexOf( str ) > -1 ) {
				result[ items[ i ].getGroup() ] = result[ items[ i ].getGroup() ] || [];
				result[ items[ i ].getGroup() ].push( items[ i ] );
			}
		}
		return result;
	};

}( mediaWiki, jQuery ) );
