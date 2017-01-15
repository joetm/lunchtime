import React from 'react';
import { render } from 'react-dom';

require("office-ui-fabric-react/dist/css/fabric.css");

import App from './Components/App.jsx';

render(
	<App />,
	document.getElementById('app')
);
