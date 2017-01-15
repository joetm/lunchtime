import React from 'react';

import AppBar from 'material-ui/AppBar';

export default class Nav extends React.Component {
	leftIconButtonTouchTap () {

	}
	render () {
		return (
			<div>
	            <AppBar
	               style={{backgroundColor:'#A0E0C0'}}
	               onLeftIconButtonTouchTap={this.leftIconButtonTouchTap.bind(this)}
	               title={this.props.sitetitle}
	               showMenuIconButton={false}
	            />
			</div>
		);
	}
}
