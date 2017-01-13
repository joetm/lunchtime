import React from 'react';

import Menuitem from './Menuitem.jsx';

class Menuitems extends React.Component {
	render () {
		if (!this.props.items) {
			return null;
		}
		console.log('this.props.items.menu', this.props.items.menu);
		const items = this.props.items.menu.map((item) => (<Menuitem item={item} />));
		return (
			<div>{items}</div>
		);
	}
}

export default Menuitems;
