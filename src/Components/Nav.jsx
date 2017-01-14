import React from 'react';

import {
  Button,
  Link,
  CommandBar
} from 'office-ui-fabric-react';

export default class Nav extends React.Component {
	leftIconButtonTouchTap () {

	}
	render () {
		return (
			<div style={{backgroundColor:'#A0E0C0'}}>
				<h1>{this.props.sitetitle}</h1>
				<CommandBar
		          searchPlaceholderText='Search...'
		          elipisisAriaLabel='More options'
		        />
			</div>
		);
	}
}
