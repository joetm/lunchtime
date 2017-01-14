import React from 'react';

import injectTapEventPlugin from 'react-tap-event-plugin';
// Needed for onTouchTap
// http://stackoverflow.com/a/34015469/988941
injectTapEventPlugin();

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';

import 'whatwg-fetch'; // see https://github.com/github/fetch

import Nav from './Nav.jsx';
import Menuitems from './Menuitems.jsx';
import LoadingAnim from './LoadingAnim.jsx';

const URL = `./data/dinnermenu.json`;

import {
  cyan500, cyan700,
  pinkA200,
  grey100, grey300, grey400, grey500,
  white, darkBlack, fullBlack,
} from 'material-ui/styles/colors';
const customTheme = {
  palette: {
    primary1Color: pinkA200,
    primary2Color: cyan700,
    primary3Color: grey400
  }
};
const theme = getMuiTheme(customTheme);

class App extends React.Component {

	constructor () {
		super();
		this.state = {
			loading: true,
			dinnermenu: null
		};
		this.serverRequest = null;
	}

	componentDidMount() {
		//fetch the data

        this.serverRequest = fetch(URL)
        .then((response) => {
            return response.json();
        }).then((dinnermenu) => {
            console.log('dinnermenu.json', dinnermenu);
            this.setState({
                dinnermenu: dinnermenu,
                loading: false
            });
        });
	}

  // abort the running request if component is unmounted
  componentWillUnmount() {
      if (this.serverRequest) {
          this.serverRequest.abort();
      }
  }

	render () {
		  return (
          <MuiThemeProvider
            	muiTheme={theme}
          >
	            <div>
        					<Nav
        						sitetitle="LunchTime"
        					/>
        					<LoadingAnim
        						loading={this.state.loading}
        					/>
        					<Menuitems
        						items={this.state.dinnermenu}
        					/>
    				  </div>
          </MuiThemeProvider>
		  );
	}

}

export default App;
