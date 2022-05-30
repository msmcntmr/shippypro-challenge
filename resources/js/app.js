import axios from "axios";

require( './bootstrap' );

import React, { Fragment, useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import {
    Autocomplete, Backdrop,
    Box, Button, Fade, FormControl, FormControlLabel, FormLabel,
    LinearProgress,
    List,
    ListItem,
    ListItemText,
    ListSubheader, Modal, Radio, RadioGroup, Stack,
    TextField
} from "@mui/material";

function App() {
    
    /**
     * @note: this is just a demo of a small SPA,
     * so I left all the logic and jsx in this file.
     * Following the best code practices, a real project
     * would have its codebase split in different files (i.e. hooks),
     * and the return() would be cleaner and smaller by
     * using components encapsulated in tags.
     *
     * Also, I imported MaterialUI in order to have
     * components already built-up and speed up the process.
     */
    
    /**
     * Endpoints
     *
     * @type {{search: string, airports: string}}
     */
    const endpoints = {
	airports: '/airports',
	search  : '/api/search'
    };
    
    /**
     * Tables structure
     *
     * @type object
     */
    const tables = {
	airports: [
	    { field: 'id', type: 'bigint, autoincrement' },
	    { field: 'name', type: 'varchar' },
	    { field: 'code', type: 'varchar, unique' },
	    { field: 'lat', type: 'varchar' },
	    { field: 'lng', type: 'varchar' }
	],
	flights : [
	    { field: 'id', type: 'bigint, autoincrement' },
	    { field: 'code_departure', type: 'varchar, fk, ref. airports.code' },
	    { field: 'code_arrival', type: 'varchar, fk, ref. airports.code' },
	    { field: 'price', type: 'decimal 6,2' },
	]
    }
    
    /**
     * Stopovers
     *
     * @type array
     */
    const stopovers = [
	{ value: 0, label: 'Direct flight' },
	{ value: 1, label: 'One stopover max' },
	{ value: 2, label: 'Two stopovers max' }
    ];
    
    /**
     * Modal style
     *
     * @type object
     */
    const modalStyle = {
	position : 'absolute',
	top      : '50%',
	left     : '50%',
	transform: 'translate(-50%, -50%)',
	width    : 400,
	bgcolor  : 'white',
	border   : '2px solid #fff',
	boxShadow: 24,
	p        : 4,
    };
    
    /**
     * App states
     */
    const [ isLoading, setIsLoading ]                         = useState( false );
    const [ airports, setAirports ]                           = useState( [] );
    const [ modalIsOpen, setModalIsOpen ]                     = useState( false );
    const [ airportFromValue, setAirportFromValue ]           = useState( null );
    const [ airportToValue, setAirportToValue ]               = useState( null );
    const [ stopoversValue, setStopoversValue ]               = useState( null );
    const [ isSearchButtonEnabled, setIsSearchButtonEnabled ] = useState( false );
    const [ searchResult, setSearchResult ]                   = useState( null );
    
    /**
     * Fetches data w/ axios.
     *
     * @param url
     * @param type
     * @param payload
     * @returns {Promise<AxiosResponse<any>>}
     */
    const fetchData = ( url, type = "post", payload ) => {
	let ax = ( type === 'get' ) ? axios.get( url, payload ) : axios.post( url, payload );
	return ax
	    .then( ( { data } ) => data )
	    .catch( error => {
		console.error( error )
	    } )
    }
    
    /**
     * Handles search button on click.
     */
    const handleSearchOnClick = () => {
	setIsLoading( true );
	setTimeout( () => {
	    fetchData( endpoints.search, 'post', {
		'from'     : airportFromValue.code,
		'to'       : airportToValue.code,
		'stopovers': stopoversValue
	    } )
		.then( data => {
		    console.log( data )
		    setSearchResult( data );
		    setIsSearchButtonEnabled( false );
		    setIsLoading( false );
		} )
	}, 2e2 )
    }
    
    /**
     * Renders stopovers block
     * @returns {JSX.Element}
     */
    function renderStopovers() {
	return (
	    <FormControl>
		<FormLabel id="demo-row-radio-buttons-group-label">Stopovers</FormLabel>
		<RadioGroup
		    row
		    aria-labelledby="demo-row-radio-buttons-group-label"
		    name="row-radio-buttons-group"
		    value={ stopoversValue }
		    onChange={ ( e, v ) => setStopoversValue( v ) }
		>
		    {
			stopovers.map( ( stopover, i ) => <FormControlLabel sx={ {
			    '& .MuiFormControlLabel-label': {
				fontSize: 12,
			    },
			} }
									    key={ i }
									    value={ stopover.value }
									    control={ <Radio size="small"/> }
									    label={ stopover.label }/> )
		    }
		</RadioGroup>
	    </FormControl>
	)
    }
    
    /**
     * Resets form fields.
     */
    const resetFields = () => {
	setAirportFromValue( null );
	setAirportToValue( null );
	setStopoversValue( null );
	setSearchResult( null );
    }
    
    /**
     * Returns whether the search button
     * should be enabled or not.
     *
     * @returns {boolean}
     */
    const isSearchEnabled = () => !!( airportFromValue && airportToValue && stopoversValue );
    
    /**
     * Runs on page load
     */
    useEffect( () => {
	setIsLoading( true );
	setTimeout( function () {
	    fetchData( endpoints.airports, 'get' ).then( data => {
		setAirports( data )
		setIsLoading( false )
	    } );
	}, 1e3 )
    }, [] )
    
    /**
     * Runs everytime form fields change.
     */
    useEffect( () => {
	setIsSearchButtonEnabled( isSearchEnabled )
    }, [ airportFromValue, airportToValue, stopoversValue ] )
    
    return (
	<Fragment>
	    <div className="bg-gray-100 w-screen h-screen flex items-center justify-center">
		<div className={ isLoading ? 'loading-screen' : 'loading-screen hidden' }>
		    <Box sx={ { width: '300px' } }>
			<LinearProgress/>
		    </Box>
		</div>
		<div className="max-w-xl">
		    <img src="/img/shippypro-logo.svg"
			 alt="ShippyPro"/>
		    <h1 className="font-semibold">ShippyPro Challenge</h1>
		    <p className="text-xs my-6">Try to create a PHP algorithm that finds the lowest price, given two
						different airport's code in tab 1, assuming at most 2 stopovers!
						At the end, represent it in a working landing page.
						(<span className="font-semibold cursor-pointer"
						       onClick={ () => setModalIsOpen( true ) }>Table structure</span>)
		    </p>
		    
		    <div className="mt-4 p-2 bg-white rounded-md drop-shadow-2xl">
			<p className="mb-2 p-2 text-sm">
			    <span className="font-semibold block">Get the cheapest air ticket fare!</span>Where do
													  you want
													  to go
													  today?</p>
			<div className="flex items-center justify-between">
			    <div className="w-1/2 p-2">
				<Autocomplete
				    disablePortal
				    id="airport-from"
				    options={ airports }
				    getOptionLabel={ ( option ) => `${ option.name } (${ option.code })` }
				    sx={ { width: '100%' } }
				    value={ airportFromValue }
				    onChange={ ( e, airport ) => setAirportFromValue( airport ) }
				    renderInput={ ( params ) => <TextField { ...params } label="From"/> }
				/>
			    </div>
			    <div className="w-1/2 p-2 bg-white">
				<Autocomplete
				    disablePortal
				    id="airport-to"
				    options={ airports }
				    getOptionLabel={ ( option ) => `${ option.name } (${ option.code })` }
				    sx={ { width: '100%' } }
				    value={ airportToValue }
				    onChange={ ( e, airport ) => setAirportToValue( airport ) }
				    renderInput={ ( params ) => <TextField { ...params } label="To"/> }
				/>
			    </div>
			</div>
			
			<div className="p-2 text-sm">
			    { renderStopovers() }
			    <div className="flex items-center justify-between pt-2">
				<div>
				    <Stack direction="row"
					   spacing={ 2 }>
					<Button variant="outlined"
						disabled={ !isSearchButtonEnabled }
						onClick={ () => handleSearchOnClick() }>Search</Button>
					<Button variant="outlined"
						onClick={ () => resetFields() }>Reset</Button>
				    </Stack>
				</div>
				<div>
				    {
					searchResult < 0 ?
					    <span className="text-gray-500">Sorry, no matching flights found.</span> :
					    searchResult ? <span><span className="block">Cheapest Air Fare found</span><span className="block font-bold text-green-700 text-lg float-right">€{searchResult}</span></span> : null
				    }
				</div>
			    </div>
			</div>
		    </div>
		
		</div>
	    
	    </div>
	    <Modal
		aria-labelledby="transition-modal-title"
		aria-describedby="transition-modal-description"
		open={ modalIsOpen }
		onClose={ () => setModalIsOpen( false ) }
		closeAfterTransition
		BackdropComponent={ Backdrop }
		BackdropProps={ {
		    timeout: 500,
		} }
	    >
		<Fade in={ modalIsOpen }>
		    <Box sx={ modalStyle }>
			<div className="flex items-stretch justify-center">
			    {
				Object.keys( tables ).map( ( k, ki ) => {
				    return (
					<div key={ ki }
					     className="mx-2">
					    <List sx={ { width: '100%', height: '100%', bgcolor: 'white' } }
						  dense={ true }
						  aria-labelledby="nested-list-subheader"
						  subheader={
						      <ListSubheader component="div"
								     id="nested-list-subheader">
							  { k.charAt( 0 ).toUpperCase() + k.slice( 1 ) } table
						      </ListSubheader>
						  }>
						{
						    tables[ k ].map( ( airport, ai ) => {
							return (
							    <ListItem key={ ai }>
								<ListItemText
								    primary={ airport.field }
								    secondary={ airport.type }
								/>
							    </ListItem>
							)
						    } )
						}
					    </List>
					</div>
				    )
				} )
			    }
			</div>
		    </Box>
		</Fade>
	    </Modal>
	</Fragment>
    );
}

export default App;

const container = document.getElementById( 'root' );
const root      = createRoot( container );
root.render( <App/> );
